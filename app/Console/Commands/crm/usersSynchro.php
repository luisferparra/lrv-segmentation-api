<?php

/**
 * Solo para ser ejecutado 1 vez... al instalar la app.
 * Carga todos los datos existentes en el CRM
 * Lo hace por PDO ya que hay demasiados campos
 */
namespace App\Console\Commands\crm;

use Illuminate\Console\Command;
use DB;
use Redis;

class usersSynchro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:preload:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Just for the installation process, get all users from the CRM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '4G');
        set_time_limit(0);
        

//Cogemos el listado de BBDDs activas
        $this->warn(" Este proceso llevará bastante tiempo... puedes ir a tomar un café ");
        $bbddListTmp = DB::connection('segmentation')->table('bbdd_lists')->where('active', 1)->select(['id', 'val'])->get();
        $arrBbdd = [];
        foreach ($bbddListTmp as $bbdd) {
            $arrBbdd[$bbdd->val] = $bbdd->id;
        }
        //Cada 1000 elementos 
        $thressHold = 2000;
       /*  \DB::listen(function ($sql) {
            var_dump($sql->sql, $sql->bindings);
        }); */
        //primero contamos para el progreess bar cuantos elementos existen
        $cont = DB::connection('crm')->table('mark_tracking_new')->where('segmentation_util', 1)->count();
        //$cont = 12000;
        $blocks = ceil($cont / $thressHold);
        $bar = $this->output->createProgressBar($blocks);
        $sql = "SELECT id_channel,bbdd_subscribed,(marketing_opener+0) as opener,(MARKETING_CLICKER + 0) as clicker, (MARKETING_PURCHASER +0) as purchaser FROM mark_tracking_new WHERE segmentation_util=b'1' and id_channel>0  AND DATE_LAST_UPDATE>='2017-10-01' limit $cont";
        $db = DB::connection('crm')->getPdo();
        $query = $db->prepare($sql);
        $query->execute();
        $this->warn(" Información Traida. Procesamos e Insertamos... Tómate otro café");

        $counter = 0;
        $arrUser = [];
        $arrOpens = [];
        $arrClickers = [];
        $arrPurchasers = [];
        $blocksCurrent = 0;
        $arrRedisOpeners = [0 => [], 1 => []];
        $arrRedisPurchasers = [0 => [], 1 => []];
        $arrRedisClickers = [0 => [], 1 => []];
        $arrBbbddList = [];
        while ($item = $query->fetch()) {
            $id = $item['id_channel'];
            /* if ($id=='4107')
                $this->info('Paramos'); */
            $inserted = false;
            $bbddList = explode(',', trim($item['bbdd_subscribed']));
            foreach ($bbddList as $bbdd) {
                if (array_key_exists($bbdd, $arrBbdd)) {
                    $id_val = $arrBbdd[$bbdd];

                    if (!array_key_exists($id_val, $arrBbbddList)) {
                        ${'dat_' . $id_val} = [];
                        $arrBbbddList[$id_val] = 1;
                    }
                    $arrUser[] = ['id' => $id, 'id_val' => $id_val];
                    ${'dat_' . $id_val}[] = $id;
                    $inserted = true;
                }
            }
            if (!$inserted) {
                //$bar->advance();
                continue;
            }
            $arrOpens[] = ['id' => $id, 'id_val' => $item['opener']];
            $arrClickers[] = ['id' => $id, 'id_val' => $item['clicker']];
            $arrPurchasers[] = ['id' => $id, 'id_val' => $item['purchaser']];

            $arrRedisOpeners[$item['opener']][] = $id;
            $arrRedisClickers[$item['clicker']][] = $id;
            $arrRedisPurchasers[$item['purchaser']][] = $id;


            $counter++;
            if ($counter >= $thressHold) {
                //$this->info(' Ejecutamos Inserción por Bloques');
                DB::connection('segmentation')->table('bbdd_users')->insertOnDuplicateKey($arrUser, ['id_val']);
                DB::connection('segmentation')->table('marketing_openers')->insertOnDuplicateKey($arrOpens, ['id_val']);
                DB::connection('segmentation')->table('marketing_clickers')->insertOnDuplicateKey($arrClickers, ['id_val']);
                DB::connection('segmentation')->table('marketing_purchasers')->insertOnDuplicateKey($arrPurchasers, ['id_val']);

//Insertamos en Redis
                foreach ($arrBbbddList as $idBbdd => $garbage) {
    # code...
                    //$this->info('A Ejecutar Users');
                    if (count(${'dat_' . $idBbdd}) > 0)
                        Redis::sadd('users:bbdd:' . $idBbdd, ${'dat_' . $idBbdd});
                    unset(${'dat_' . $idBbdd});
                }
                unset($arrBbbddList);
                if (!empty($arrOpeners[0])) Redis::sadd('crm:marketing_openers:0', $arrOpeners[0]);
                if (!empty($arrOpeners[1])) Redis::sadd('crm:marketing_openers:1', $arrOpeners[1]);
                if (!empty($arrClickers[0])) Redis::sadd('crm:marketing_clickers:0', $arrClickers[0]);
                if (!empty($arrClickers[1])) Redis::sadd('crm:marketing_clickers:1', $arrClickers[1]);
                if (!empty($arrPurchasers[0])) Redis::sadd('crm:marketing_purchasers:0', $arrPurchasers[0]);
                if (!empty($arrPurchasers[1])) Redis::sadd('crm:marketing_purchasers:1', $arrPurchasers[1]);

                unset($arrRedisOpeners);
                unset($arrRedisClickers);
                unset($arrRedisPurchasers);


                unset($arrUser);
                unset($arrOpens);
                unset($arrClickers);
                unset($arrPurchasers);
                $counter = 0;
                $arrUser = [];
                $arrOpens = [];
                $arrClickers = [];
                $arrPurchasers = [];
                $arrBbbddList = [];
                $arrRedisOpeners = [0 => [], 1 => []];
                $arrRedisPurchasers = [0 => [], 1 => []];
                $arrRedisClickers = [0 => [], 1 => []];
                $blocksCurrent++;

                $bar->advance();

            }

        }
        /**
         * Puede que tengamos datos que no hayamos insertado, del último bloque 
         */
        if (!empty($arrUser)) {
            DB::connection('segmentation')->table('bbdd_users')->insertOnDuplicateKey($arrUser, ['id_val']);

        }
        if (!empty($arrOpens)) {
            DB::connection('segmentation')->table('marketing_openers')->insertOnDuplicateKey($arrOpens, ['id_val']);
        }
        if (!empty($arrClickers)) {
            DB::connection('segmentation')->table('marketing_clickers')->insertOnDuplicateKey($arrClickers, ['id_val']);

        }
        if (!empty($arrPurchasers)) {
            DB::connection('segmentation')->table('marketing_purchasers')->insertOnDuplicateKey($arrPurchasers, ['id_val']);
        }
        foreach ($arrBbbddList as $idBbdd => $garbage) {
            # code...
            if (count(${'dat_' . $idBbdd}) > 0)
                Redis::sadd('users:bbdd:' . $idBbdd, ${'dat_' . $idBbdd});
            unset(${'dat_' . $idBbdd});
        }
        unset($arrBbbddList);
        if (!empty($arrOpeners[0])) Redis::sadd('crm:marketing_openers:0', $arrOpeners[0]);
        if (!empty($arrOpeners[1])) Redis::sadd('crm:marketing_openers:1', $arrOpeners[1]);
        if (!empty($arrClickers[0])) Redis::sadd('crm:marketing_clickers:0', $arrClickers[0]);
        if (!empty($arrClickers[1])) Redis::sadd('crm:marketing_clickers:1', $arrClickers[1]);
        if (!empty($arrPurchasers[0])) Redis::sadd('crm:marketing_purchasers:0', $arrPurchasers[0]);
        if (!empty($arrPurchasers[1])) Redis::sadd('crm:marketing_purchasers:1', $arrPurchasers[1]);
        $bar->finish();
        $this->info(' Finished ');
    }
}
