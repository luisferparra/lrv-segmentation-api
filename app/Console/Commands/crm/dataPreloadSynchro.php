<?php

/**
 * Comando que dado un id de aaatablecontrol y un nombre de campo del crm, trae los datos y los procesa
 * @date 2018.03.13
 *      Añadimos la inserción de datos en Redis
 */
namespace App\Console\Commands\crm;

use Illuminate\Console\Command;
use DB;
use Redis;
use Carbon\Carbon;
use App\Models\AaaaTableControl;

class dataPreloadSynchro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:preload:field 
    {tableControl : Id of the Control Table} 
    {crmField : Name of the field at the CRM}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Given an id from the Control Table and a Remote Field, system will update all data from the CRM';

    protected $tableControl;
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
        /**
         * Este dato mide cada cuantos datos traídos se insertan o se actualizan las tablas
         */
        $thressHold = 2000;

        $tableControlObj = AaaaTableControl::findOrFail($this->argument('tableControl'));
        $tableName = $tableControlObj->name;
        $tableNameVals = $tableName . config('api-crm.table_val_postfix');
        $apiName = trim($tableControlObj->api_name);
        $redisApiName = 'crm:' . $tableName . ':';


        /**
         * Chequeamos que el tablecontrol sea del tipo correcto
         */
        $tableType = $tableControlObj->action;
        if ($tableType == 'bit' || $tableType == 'ignore') {
            $this->error('Local table type not allowed for this operation');
            return;
        }
        /**
         * First we will check if the field exists at the CRM and the information linked to that field
         */
        $crmField = $this->argument('crmField');
        $crmFieldObj = DB::connection('crm')->table('pixel_column')->where('columna', $crmField)->get();

        if (count($crmFieldObj) != 1) {
            $this->error('Field at the CRM not found');
            return;
        }
        $crmType = $crmFieldObj[0]->update_type;
        $crmId = $crmFieldObj[0]->id_column;
        $crmTable = $crmFieldObj[0]->table_ref;
        $crmKeys = $crmFieldObj[0]->key_value_ref;
        /**
         * Array para inserci´no de los datos relacionales
         */
        $arrValues = [];
        /**
         * Array que contendrá los datos referenciales existentes
         */
        $arrRefValues = [];
        $this->warn('Cargamos los datos referneciales del CRM');
        if (!empty($crmTable)) {
            //Los datos los cogemos de la tabla referencia.
            $tmp = explode(',', $crmKeys);
            if (count($tmp) < 2) {
                $this->error('Field key_value_ref from pixel_column not well formated');
                return;
            }
            $crmTableId = $tmp[0];
            $crmTableVal = $tmp[1];
            $crmRefDataObj = DB::connection('crm')->table($crmTable)->get();
            //Estamos aquí no sabemos si existirán datos que sean solo para emailing o teléfono. Cogemos y vemos las keys
            foreach ($crmRefDataObj as $val) {
                # code...
                if (array_key_exists('channel_type', $val)) {
                        //existe channeltype en los datos. cogemos solo los que tengan 1 entre los datos
                    $crmChannelType = $val->channel_type;
                    if (strpos($val->channel_type, '1') === false) {
                        //$this->info('saltadop');
                        continue;
                    }
                    //$this->info('no saltado');
                }
                $idCrm = $val->$crmTableId;
                $valueCrm = trim($val->$crmTableVal);
                $arrValues[] = ['val_crm' => $idCrm, 'val_normalized' => $valueCrm];


            }



        } else {
            //Los valors de referencia los cogemos de la tabla pixel_column_values
            $crmRefDataObj = DB::connection('crm')->table('pixel_column_values')->where('id_column', $crmId)->orderBy('columna_value')->get();
            foreach ($crmRefDataObj as $val) {
                $arrValues[] = ['val_crm' => $val->columna_value, 'val_normalized' => $val->columna_value];
            }
        }
        //los guardamos
        if (!empty($arrValues)) {
            DB::connection('segmentation')->table($tableNameVals)->insertOnDuplicateKey($arrValues, ['val_crm', 'val_normalized']);
        }
        $this->warn(' Datos Referenciales Guardados ');
        //Aquí ya hemos cargado los datos en la tabla de valores. 
        //Vamos a coger en un array key->value los datos a insertar
        $tmp = DB::connection('segmentation')->table($tableNameVals)->get();
        foreach ($tmp as $val) {
            $arrRefValues[$val->val_crm] = $val->id;
        }
        $this->warn(' Traemos los datos del CRM... te recomiendo que te vayas a por un café');
        $cont = DB::connection('crm')->table('mark_tracking_new')->where($crmField, '>', "''")->where('segmentation_util', 1)->count();
        $blocks = ceil($cont / $thressHold);
        $blockCurrent = 0;

        /**
         * La query la tenemos que hacer por PDO
         */


        $sql = "SELECT id_channel,$crmField as value FROM mark_tracking_new WHERE segmentation_util=b'1' and id_channel>0 and $crmField>'' limit $cont";
        $db = DB::connection('crm')->getPdo();
        $query = $db->prepare($sql);
        $query->execute();
        $this->warn('DAtos traídos... los procesamos. Quédate un rato y si la barra va lenta, te piras a por un café.');
        $bar = $this->output->createProgressBar($blocks);
        /**
         * Array para insertar los datos
         */
        $arrIns = [];
        //Contador de elementos procesados
        $counter = 0;

        /**
         * Recorremos los datos traídos para procesar
         */
        $arrApisRedis = [];
        while ($item = $query->fetch()) {
            $id = $item['id_channel'];



//Comprobamos la integridad del dato
            /* if ($id == '4107')
                $this->warn('Paramos Máquinas'); */
            /* $resExists = DB::connection('segmentation')->table('bbdd_users')->find($id);
//Si no lo encontramos, continuamos y lo ignoramos.
            if ($resExists === null)
                continue; */


            $inserted = false;
            $valList = explode(',', trim($item['value']));
            foreach ($valList as $item) {
                if (array_key_exists($item, $arrRefValues)) {
                    $id_val = $arrRefValues[$item];
                    if (!array_key_exists($id_val, $arrApisRedis)) {
                        $arrApisRedis[$id_val] = 1;
                        ${'val_' . $id_val} = [];
                    }
                    $arrIns[] = ['id' => $id, 'id_val' => $id_val];
                    ${'val_' . $id_val}[] = $id;
                    $inserted = true;
                }
            }
            if (!$inserted) {
                //$bar->advance();
                continue;
            }

            $counter++;
            if ($counter >= $thressHold) {

                //DB::connection('segmentation')->table($tableName)->insertOnDuplicateKey($arrIns, ['id_val']);
                DB::connection('segmentation')->table($tableName)->insertIgnore($arrIns);
                $counter = 0;
                unset($arrIns);
                $arrIns = [];
                $bar->advance();
                $blockCurrent++;
                if ($blockCurrent == floor($blocks / 4))
                    $this->warn(' mmmm 25% y aún sin café...YO si fuera tú iría a por uno ');
                elseif ($blockCurrent == floor($blocks / 2))
                    $this->warn(' Un 50%!! ánimo ');
                elseif ($blockCurrent == 3 * floor($blocks / 4))
                    $this->warn(' Owowowoww 75%!! Quién te lo hubiera dicho hace un rato... ya casi está ');
                //Insertamos en REdis
                foreach ($arrApisRedis as $idKey => $garbage) {
                    Redis::sadd($redisApiName . $idKey, ${'val_' . $idKey});
                    unset(${'val_' . $idKey});
                }
                unset($arrApisRedis);
                $arrApisRedis = [];
            }
        }
        if (!empty($arrIns)) {
            DB::connection('segmentation')->table($tableName)->insertOnDuplicateKey($arrIns, ['id_val']);
             //Insertamos en REdis
            foreach ($arrApisRedis as $idKey => $garbage) {
                Redis::sadd($redisApiName . $idKey, ${'val_' . $idKey});
                unset(${'val_' . $idKey});
            }
            unset($arrApisRedis);
            $arrApisRedis = [];
        }
        $bar->finish();
        $this->info(' Finished ');


    }
}
