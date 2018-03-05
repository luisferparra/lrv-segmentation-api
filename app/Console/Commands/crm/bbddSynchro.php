<?php
/**
 * Comando que sincroniza las tablas de bbdds disponibles y existentes en el CRM
 */
namespace App\Console\Commands\crm;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

class bbddSynchro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:sync:bbdd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize local DB with CRM DB';

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
        //
        $arrIns = [];
        $crmDb = DB::connection('crm')->table('admin_bbdd')->select(['id_bbdd','slug','b_activa'])->get();
        foreach ($crmDb as $db) {
            $id = $db->id_bbdd;
            $slug = strtoupper(trim($db->slug));
            $active = $db->b_activa;
            $item = ['id'=>$id,'val'=>$slug,'active'=>$active];
            $arrIns[] = $item;

        }
       /*  \DB::listen(function($sql) {
            var_dump($sql->sql,$sql->bindings);
        }); */
        DB::connection('segmentation')->table('bbdd_lists')->insertOnDuplicateKey($arrIns,['val','active','updated_at'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }
}
