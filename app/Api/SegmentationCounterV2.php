<?php

/**
 * Clase de segmentación
 * 
 * Esta vez la forma de recoer los datos serán distintas. Crearemos tablas temporales donde insertaremos los datos
 */

namespace App\Api;

use Schema;
use DB;
use App\Models\AaaaTableControl;
use App\Models\SegmentationCounterRequest;
use Illuminate\Support\Facades\Storage;

class SegmentationCounterV2 implements SegmentationCounterInterface
{
    /**
     * Objeto de entrada. se corresponde con una row de la tabla data_loads
     *
     * @var collection
     */
    protected $objRequest = null;
    /**
     * Petición original en formato array
     *
     * @var array
     */
    protected $originalRequest = [];
    /**
     * Variable que definirá el postfix de las tablas de valores
     *
     * @var string
     */
    protected $tablePostFix = '';
    /**
     * Array que contiene los distintos errores encontrados
     *
     * @var array
     */
    protected $errors = [];
    /**
     * Listado de columnas Ignoradas
     *
     * @var array
     */
    protected $columnsIgnored = [];
    /**
     * Uuid generado y que creará los ficheros
     *
     * @var string
     */
    protected $uuid = '';
    /**
     * Array que tendrá como clave el id d ela bbdd y como valor su slug en uppercase
     *
     * @var array
     */
    protected $arrBbddList = [];




    /**
     * Resputa a devolver
     *
     * @var array
     */
    protected $response = null;
    /**
     * Http status a devolver en la request del contador
     *
     * @var integer
     */
    protected $http_status = 200;




    protected function calculateOutput()
    {
    //probamos un insert ignore con duplicate key y todo... ozú
        $arrOutputBBDD = [];

        $bbddList = $this->getBbddList();

        $inputDataType = $this->originalRequest['input_data_mode'];
        $bbddStrict = (bool)$this->originalRequest['bbdd_strict_order'];
        $kid = (empty($this->originalRequest['kid'])) ? '' : $this->originalRequest['kid'];
   // $uuid = $this->originalRequest['uuid_token'];
    //No se para qué lo voy a necesitar
        $originalUuid = empty($this->originalRequest['token']) ? '' : trim($this->originalRequest['token']);
        $limits = $this->originalRequest['limits'];
        $limitTot = (!empty($limits['limit']) && is_numeric($limits['limit'])) ? (int)$limits['limit'] : false;

        $bbddListInput = $this->originalRequest['bbdd'];
        $bbddListOutput = [];
        foreach ($bbddListInput as $bbdd) {
            $this->arrBbddList[$bbddList[$bbdd]] = $bbdd;
            ${'bbdd_' . strtoupper($bbdd)} = [];
            ${'bbdd_idchann_' . strtoupper($bbdd)} = [];
            $arrOutputBBDD[$bbdd] = ['tot' => 0, 'unique' => 0];
        }
        $data = $this->originalRequest['segmentation'];
        if (empty($data) || empty($data['data'])) {
            $this->errors[] = ['msg' => 'Empty Input Data', 'errorCode' => 421];
            return false;
        }
        $idChannelList = [];
        $idArr = 0;
        $numberOfSegmentations = 0;
        foreach ($data['data'] as $datum) {
        # code...
            $arr_temporary = [];
            $api_name = $datum['api_name'];
            $values = $datum['values'];
        //Comprobamos que podemos segmentar por dicha api
            $apiNameData = AaaaTableControl::where('api_name', $api_name)->get();

            if ($apiNameData->count() != 1) {
                $this->columnsIgnored[] = $api_name;
                $this->errors[] = ['msg' => 'Ignored Column (Not Found): ' . $api_name, 'errorCode' => 404];
                continue;
            } elseif ($apiNameData[0]->action == 'ignored') {
                $this->columnsIgnored[] = $api_name;
                $this->errors[] = ['msg' => 'Ignored Column (Operation Not Allowed): ' . $api_name, 'errorCode' => 403];
                continue;
            } elseif ((is_array($values) && count($values) == 0) || $values == '') {
                $this->columnsIgnored[] = $api_name;
                $this->errors[] = ['msg' => 'Ignored Column (Empty Values): ' . $api_name, 'errorCode' => 421];
                continue;
            }
            $isBit = $apiNameData[0]->action == 'bit';
            if ($isBit) {
                if (is_array($values) && (!is_numeric($values[0]) || !($values[0] == 1 || $values[0] == 0))) {
                    $this->columnsIgnored[] = $api_name;
                    $this->errors[] = ['msg' => 'Ignored Column (Value not admited): ' . $api_name, 'errorCode' => 421];
                    continue;
                }
                $values = (is_array($values)) ? $values[0] : $values;
            }
            $this->putDataIntoTemporaryTable($apiNameData[0], $inputDataType, $values);
            $numberOfSegmentations++;
        }
        //Ahora cogemos los datos
        $start = microtime(true);
        //$this->removeUnusefulDataFromTemporaryTable($numberOfSegmentations);
        $this->initiateLongQueries();
        $res = DB::connection('segmentation')->table(md5($this->uuid) . ' as tmp')->
        join('bbdd_users', function ($join) {
            $join->on('bbdd_users.id', '=', 'tmp.id')
                 ->whereIn('bbdd_users.id_val', array_keys($this->arrBbddList));
        })->
        where('used', $numberOfSegmentations)->select(DB::raw("group_concat(bbdd_users.id separator ',') as id, bbdd_users.id_val"))->groupBy("bbdd_users.id_val")->get();
        $end = microtime(true);
        foreach ($res as $k => $v) {
    # code...


            $idBbdd = $v->id_val;
            $bbdd = $this->arrBbddList[$idBbdd];
            if (isset(${'bbdd_' . strtoupper($bbdd)})) {
                $tmp = explode(',', $v->id);
                foreach ($tmp as $idChann) {
                    ${'bbdd_' . strtoupper($bbdd)}[$idChann] = ['id' => $idChann, 'bbdd' => $idBbdd];

                }

            }


        }

//Aquí ya tenemos los arrays con los datos correspondientes.
        $arrUsedIds = [];
        $arrUsedIdsAll = [];
        $limitAct = 0;
        foreach ($this->arrBbddList as $idBbdd => $bbdd) {
            if (isset(${'bbdd_' . strtoupper($bbdd)})) {
                foreach (${'bbdd_' . strtoupper($bbdd)} as $id => &$garbage) {
                    if ((($limitTot && $limitAct < $limitTot) || $limitTot === false) && !array_key_exists($id, $arrUsedIds)) {
                        ${'bbdd_idchann_' . strtoupper($bbdd)}[$id] = 1;
                        $arrUsedIds[$id] = $idBbdd;
                        $limitAct++;
                    }
                    $arrUsedIdsAll[$id] = 1;
                }
            }
        }
        $output = [];
        $output['totitems'] = count($arrUsedIds);
        unset($arrUsedIds);
        $output['totsegmentation'] = count($arrUsedIdsAll);
        unset($arrUsedIdsAll);
        $output['data'] = [];
        foreach ($this->arrBbddList as $idBbdd => $bbdd) {
            $fileUrl = '';
    //Generamos los ficheros
            $md5 = md5($this->uuid);
//Para guardar los ficheros, se genera el md5 del uuid. El directorio estará formado por el caracter 1 del md5, subdirectorio los 2 siguientes caracteres
           /* $fileName = $md5[0] . '/' . $md5[1] . $md5[2] . '/' . $this->uuid . '-' . strtoupper($bbdd) . '.csv';
            $res = Storage::disk('segmentationFilesRaw')->put($fileName, implode("\r\n", array_keys(${'bbdd_' . strtoupper($bbdd)})) . "\r\n", 'public');
            
            if (count(${'bbdd_idchann_' . strtoupper($bbdd)}) > 0) {
                $res = Storage::disk('segmentationFiles')->put($fileName, implode("\r\n", array_keys(${'bbdd_idchann_' . strtoupper($bbdd)})) . "\r\n", 'public');
                $fileUrl = Storage::disk('segmentationFiles')->url($fileName);
            }
            */
            $tot = (isset(${'bbdd_' . strtoupper($bbdd)})) ? count(${'bbdd_' . strtoupper($bbdd)}) : 0;
            $unique = (isset(${'bbdd_idchann_' . strtoupper($bbdd)})) ? count(${'bbdd_idchann_' . strtoupper($bbdd)}) : 0;
            $output['data'][] = ['idbbdd' => $idBbdd, 'bbdd' => $bbdd, 'tot' => $tot, 'unique' => $unique, 'urlFile' => $fileUrl];
   
   
//  $arrOutputBBDD[$bbdd]['tot'] = (isset(${'bbdd_' . strtoupper($bbdd)})) ? count(${'bbdd_' . strtoupper($bbdd)}) : 0;
//  $arrOutputBBDD[$bbdd]['unique'] = (isset(${'bbdd_idchann_' . strtoupper($bbdd)})) ? count(${'bbdd_idchann_' . strtoupper($bbdd)}) : 0;
        }
        $output['errors'] = ['numErrors' => count($this->errors), 'data' => $this->errors];
        dd($output);
        return $output;



        dd($res);





    }


    protected function putDataIntoTemporaryTable($obj, $inputDataType, &$values)
    {

        $tableName = trim($obj->name);
        $tableNameVals = $tableName . $this->tablePostFix;
        $dataType = $obj->data_type_id;
        $isBit = $obj->action == 'bit';
        $column = ($inputDataType == 'id') ? 'id' : ($inputDataType == 'crm' ? 'val_crm' : 'val_normalized');

        if ($isBit) {
            $q = "SELECT  id FROM `" . $tableName . "` WHERE val=b'" . $values . "'";
        } elseif ($column == 'id') {
                //Si se busca por ids, nos ahorramos un inner join
            $q = "SELECT id FROM `" . $tableName . "` WHERE id_val in (" . implode(',', $values) . ")";
        } else {
            $q = "SELECT tbla.id FROM `" . $tableName . "` AS tbla INNER JOIN `" . $tableNameVals . "` AS vals ON tbla.id_val = vals.id AND vals." . $column . " IN (" . implode(',', $values) . ")";
        }
        $start = microtime(true);
       // $q = "insert into " . md5($this->uuid) . " (id) (".$q.") ON DUPLICATE KEY update used=1";
      //  dd($q);
        DB::connection('segmentation')->insert("insert into `" . md5($this->uuid) . "` (id) (" . $q . ") ON DUPLICATE KEY update used=used+1");
        $end = microtime(true);
        dump(($end - $start));
    }

    protected function removeUnusefulDataFromTemporaryTable($number)
    {
    //Mejoraremos la query final con esto??
        DB::connection('segmentation')->table(md5($this->uuid))->where('used', '!=', $number)->delete();
    }

    /**
     * Funcón que crea una tabla temporal en el esquema temporal
     */
    protected function createTemporaryTable()
    {
        $table = md5($this->uuid);
        Schema::connection('segmentation')->create($table, function ($table) {
            $table->integer('id')->unasigned()->primary()->references('id')->on('bbdd_users');
            $table->tinyInteger('used')->unsigned()->index()->default(1);
        
        //$table->primary(['id', 'bbdd']);
        });

    }

    protected function dropTemporaryTable()
    {
        Schema::connection('segmentation')->dropIfExists(md5($this->uuid));
    }


    /**
     * Función necesaria para permitir que lasqueries devuelvan gran cantidad de datos
     *
     * @return void
     */
    protected function initiateLongQueries()
    {
        DB::connection('segmentation')->select(DB::raw("SET SESSION group_concat_max_len = 100000000;"));

    }

    /**
     * Constructor de la clase
     */
    public function processRequest(SegmentationCounterRequest $request)
    {
        ini_set('memory_limit', '4G');
        set_time_limit(0);
        $this->tablePostFix = config('api-crm.table_val_postfix');
        $this->objRequest = $request;
        $a = $request['request'];
        $this->originalRequest = json_decode($request['request'], true);
        $this->uuid = $request['uuid_token'];

        $this->createTemporaryTable();
        $this->calculateOutput();
        dd($request);
        //blablabla
        $this->dropTemporaryTable();
//        $this->createTemporaryTable($this->uuid);
//        $return = $this->calculateOutput();
//        return $return;
        //Schema::connection('temp')->dropIfExists($this->uuid);

    }


    /**
     * Función privada que devuelve un array con las bbdds del sistema
     *
     * @return array con la información de las bbdds. BBDD=>ID
     */
    protected function getBbddList()
    {
        $out = [];
        $bbddTmp = DB::connection('segmentation')->table('bbdd_lists')->get();
        foreach ($bbddTmp as $key => $bbddItem) {
            $out[$bbddItem->val] = $bbddItem->id;
        }
        return $out;
    }


}