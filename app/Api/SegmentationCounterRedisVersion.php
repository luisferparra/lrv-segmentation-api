<?php

/**
 * Clase de segmentación
 * 
 * la segmentación se realiza utilizando Redis como proveedor de datos
 * 
 */

namespace App\Api;

use Schema;
use DB;
use Redis;
use App\Models\AaaaTableControl;
use App\Models\SegmentationCounterRequest;
use Illuminate\Support\Facades\Storage;

class SegmentationCounterRedisVersion implements SegmentationCounterInterface
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
     * Array que contendrá todas las colecciones temporales creadas y que se tendrán que borrar antes de devolver los datos
     *
     * @var array
     */
    protected $redisTempCollections = [];

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
    /**
     * Prefijo de los datos de segmentación existentes en Redis
     *
     * @var string
     */
    protected $redis_segmentation_prefix = '';
    /**
     * Prefijo de los datos de usuarios de cada bbdd
     *
     * @var string
     */
    protected $redis_users_prefix = '';
    /**
     *  Array donde se introducirán las tablas temporales creadas en redis, para su posterior eliminación
     *
     * @var array
     */
    protected $redis_arr_temps = [];

    /**
     * Función que calcula la salida dados los datos de entrada
     *
     * @return void
     */
    protected function calculateOutput()
    {
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

        //Aquí tenemos el listado de bbdds requeridas por orden
        $bbddListInput = $this->originalRequest['bbdd'];
        $bbddListOutput = [];
        foreach ($bbddListInput as $bbdd) {
            $this->arrBbddList[$bbddList[$bbdd]] = $bbdd;
            ${'bbdd_' . strtoupper($bbdd)} = [];
            ${'bbdd_idchann_' . strtoupper($bbdd)} = [];
            $arrOutputBBDD[$bbdd] = ['idbbdd'=>$bbddList[$bbdd],'bbdd'=>$bbdd,'tot' => 0, 'unique' => 0,'urlFile'=>''];
        }
        $data = $this->originalRequest['segmentation'];
        if (empty($data) || empty($data['data'])) {
            $this->errors[] = ['msg' => 'Empty Input Data', 'errorCode' => 421];
            return false;
        }
        $idChannelList = [];
        $idArr = 0;

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
            $tble = $apiNameData[0]->name;
            $tmpItem = 'temp:' . $this->uuid . ':' . $tble;
            $this->redis_arr_temps[] = $tmpItem;
            $arrTmp = [];
            if (is_array($values)) {
                foreach ($values as $value) {
                    $arrTmp[] = $this->redis_segmentation_prefix . $tble . ':' . $value;
                }
            } else {
                $arrTmp[] = $this->redis_segmentation_prefix . $tble . ':' . $values;
            }

            Redis::sunionstore($tmpItem, $arrTmp);
            unset($arrTmp);


        }// foreach ($data['data'] as $datum) { 
        Redis::sinterstore('temp:' . $this->uuid, $this->redis_arr_temps);
        //en temp:uuid tenemos todos los registros que podemos procesar en total con la segmentaci´no existente.
        $totalItems = Redis::scard('temp:' . $this->uuid);
        $this->redis_arr_temps[] = 'temp:' . $this->uuid;
        $start = microtime(true);


        $limitTot = ($limitTot == false) ? $totalItems : $limitTot;
            //Ahora recorremoscada BBDD para hacer la intersección de los datos y ver los que concuerdan exactamente 

        $position = 0;
        $currentCont = 0;
        /**
         * Colección donde tendremos TODOS los elementos temporales según necesidad
         * Se utilizará solo para almacenaje y ver lo que se ha insertado
         */
        $tempAllItems = 'temp:temp:' . $this->uuid . ':' . rand();
        $this->redis_arr_temps[] = $tempAllItems;
        foreach ($this->arrBbddList as $idBbdd => $bbdd) {
               # code...
            /**
             * En Key guardamos los datos que se podrían seleccionar y que concuerdan con la segmentación de esa BBDD
             */
            $key = 'temp:' . $this->uuid . ':bbdd:' . $idBbdd;
            $key_temp = 'temp:' . $this->uuid . ':sdiffstore:bbdd:' . $idBbdd;
            Redis::sinterstore($key, ['temp:' . $this->uuid, $this->redis_users_prefix . $idBbdd]);
            //$arrOutputBBDD[$bbdd]['tot'] = Redis::scard($key);
            /**
             * Guardamos en este array los datos que cumplenn de esa bbdd la segmentación realizada
             */
            //${'bbdd_' . strtoupper($bbdd)} = Redis::smembers($key);
            //$arrOutputBBDD[$bbdd]['tot'] = count(${'bbdd_' . strtoupper($bbdd)});
            $arrOutputBBDD[$bbdd]['tot'] = Redis::scard($key);
            //Vamos ahora con los límites
            if ($position == 0) {
                /**
                 * Al ser la primera iteracción, introducimos TODOS los elementos de la segmentación como los elegidos. 
                 */
                Redis::sunionstore($tempAllItems, [$key, 'blablablibli' . rand()]);
                $arrOutputBBDD[$bbdd]['unique'] = Redis::scard($tempAllItems);
                //${'bbdd_idchann_' . strtoupper($bbdd)} = ${'bbdd_' . strtoupper($bbdd)};
            } elseif ($currentCont < $limitTot) {

                $temptemp = $tempAllItems . ':sinterstore:' . $idBbdd;

                Redis::sdiffstore($key_temp, [$key, $tempAllItems]);
                $arrOutputBBDD[$bbdd]['unique'] = Redis::scard($key_temp);
                
                //${'bbdd_idchann_' . strtoupper($bbdd)} = Redis::smembers($key_temp);
                Redis::sunionstore($tempAllItems, [$tempAllItems, $key_temp]);
               
                /* Redis::sinterstore($temptemp, Redis::sinter([$key, $tempAllItems]));
                Redis::sdiffstore($key_temp, [$key, $temptemp]);
                Redis::sunionstore($tempAllItems, [$tempAllItems, $key_temp]);
                ${'bbdd_idchann_' . strtoupper($bbdd)} = Redis::smembers($key_temp); */
                $this->redis_arr_temps[] = $temptemp;

            }
            $currentCont = Redis::scard($tempAllItems);
            if ($currentCont > $limitTot) {
                $toRemove = $currentCont - $limitTot;
                $t = count(${'bbdd_idchann_' . strtoupper($bbdd)});
               // if ($t > $diff) {
                    ${'bbdd_idchann_' . strtoupper($bbdd)} = array_splice(${'bbdd_idchann_' . strtoupper($bbdd)}, 0, ($t-$toRemove));

                //}
            }
           /*  $fileName = $this->uuid[0] . '/' . $this->uuid[1] . $this->uuid[2] . '/' . $this->uuid . '-' . strtoupper($bbdd) . '.csv';
            $res = Storage::disk('segmentationFilesRaw')->put($fileName, implode("\r\n", ${'bbdd_' . strtoupper($bbdd)}) . "\r\n", 'public');
            if (count(${'bbdd_idchann_' . strtoupper($bbdd)}) > 0) {
                $res = Storage::disk('segmentationFiles')->put($fileName, implode("\r\n", ${'bbdd_idchann_' . strtoupper($bbdd)}) . "\r\n", 'public');
                $fileUrl = Storage::disk('segmentationFiles')->url($fileName);
                $arrOutputBBDD[$bbdd]['urlFile'] = $fileUrl;
            }

            $arrOutputBBDD[$bbdd]['unique'] = count(${'bbdd_idchann_' . strtoupper($bbdd)}); */
            $position++;
            $this->redis_arr_temps[] = $key;
            $this->redis_arr_temps[] = $key_temp;


        } 
        //Eliminamos todos los items temporales creados
        $this->__redisRemoveTemporaryCollections();
        /* dump((microtime(true) - $start));
        dump($arrOutputBBDD); */
        $totSegmentation = ($currentCont>$limitTot) ? $limitTot : $currentCont;
        return ['totitems'=>$totalItems,'totsegmentation'=>$totSegmentation,'data'=>$arrOutputBBDD];
    }

    /**
     * Función que eliminará todas las tablas temporales creadas
     *
     * @return void
     */
    protected function __redisRemoveTemporaryCollections()
    {
        $this->redis_arr_temps[] = 'temp:' . $this->uuid;
        Redis::del($this->redis_arr_temps);

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

    /**
     * Constructor de la clase
     */
    public function processRequest(SegmentationCounterRequest $request)
    {
        ini_set('memory_limit', '4G');
        set_time_limit(0);
        $start = microtime(true);
        $this->tablePostFix = config('api-crm.table_val_postfix');
        $this->redis_segmentation_prefix = config('api-crm.redis_crm_prefix');
        $this->redis_users_prefix = config('api-crm.redis_users_prefix');
        
        $this->objRequest = $request;
        $a = $request['request'];
        $this->originalRequest = json_decode($request['request'], true);
        $this->uuid = $request['uuid_token'];
        $return = $this->calculateOutput();
        $return['totTime'] = microtime(true)-$start;
        //dump($return);
        return $return;

    }


    /**
     * Getter del http status a devolver
     *
     * @return integer
     */
    public function getHttpStatus()
    {
        return $this->http_status;
    }

    /**
     * Getter de la respuesta
     *
     * @return void
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * Getter del array co resumen de errroes
     *
     * @return void
     */
    public function getErrors()
    {
        return $this->errors;
    }

}