<?php

/**
 * Clase que gestionará el insert/update/remove (Bulk Data Load) de la api de entrada
 * Debe gestionar lo que se encuentra en la tabla data_loads (se lanza por jobs)
 * 
 */
namespace App\Api;

use Schema;
use DB;
use App\Models\DataLoad;
use App\Models\AaaaTableControl;




class LoadDataProcess
{

    /**
     * Objeto de entrada. se corresponde con una row de la tabla data_loads
     *
     * @var collection
     */
    protected $dataLoadObj = null;
    /**
     * Variable que definirá el postfix de las tablas de valores
     *
     * @var string
     */
    protected $tablePostFix = '';

    /* ********************************** VARIABLES PARA RETORNAR LOS DATOS *****************************/
    /**
     * Array con los posibles errores encontrados
     *
     * @var array
     */
    protected $errors = [];
    /**
     * Número de elementos procesados
     *
     * @var integer
     */
    protected $processedItems = 0;

/**
 * Array que contendrá información adicional, por ejemplo, usuarios procesados en total y actualizaciones total
 *
 * @var array
 */
    protected $processedUsers = 0;

    /**
     * Función que es llamada cuando se recibe una cantidad ingente de datos, ordenados según segmentación. Es decir, todos los inserts son relativos a una segmentación únicamente
     *
     * @return true
     */
    protected function createUsersByApiNameCRMData()
    {
        $errors = [];
        //Recogemos la api_name y la request original para poder procesar.
        $api_name = $this->dataLoadObj->api_name;
        $data = AaaaTableControl::where('api_name', $api_name)->limit(1)->get();

        $tableName = $data[0]->name;

        $tableValsName = $tableName . $this->tablePostFix;

        /**
         * Acción de la tabla.
         * Pueden tener los valores de bit, ignore, normal (habrá más en el futuro).
         * Si es Ignore no se hace nada. Si es bit es único además.
         */
        $dataActonType = $data[0]->action;
        if ($dataActonType == 'ignore') {
            $errors[] = array('id' => 0, 'msg' => 'Operation not allowed on this segmentation', 'val' => '');
            $this->errors = $errors;
            return;
        }

        /**
         * Tipos de datos que pueden existir en la tabla. 
         * 1::simple
         * 2::múltiple
         * 
         * Si es Simple (1) Tendremos que borrar los datos existentes y actualizar los nuevos, o actualizar directamente los existentes
         */
        $dataTypeId = $data[0]->data_type_id;


        $originalRequest = json_decode($this->dataLoadObj->request, true);
        $inputDataType = $originalRequest['input_data_mode']; //Posibles valores: id, crm, val.Dependeráde lo que venga buscaremos la existenia del dato en campos distintos
        $data = $originalRequest['data'];
        
//Array donde meteremos las tuplas para insertar.
        $arrIns = [];
        $arrInputIds = [];
        //Recorremos Data que contiene los datos dle usuario para ser insertados
        foreach ($data as $datum) {
            # code...
            /**
             * $id: Valor a actualizar. Dependiendo del  inputDataType el campo puede ser el propio id, el valor del crm o el valor de val.
             * $val: Array de idchannels
             */
            $id = $datum['id'];
            $val = $datum['val'];
            if ($dataActonType != 'bit') {
                $idVal = $this->__getValueId($tableValsName, $id, $inputDataType);
                if ($idVal === false) {
                //Ha habido un error. no ha encontrado el dato
                    $errors[] = array('id' => $id, 'msg' => 'ID Value not found', 'val' => $val);
                    continue;
                }

            } else {
                if (!(is_bool($id) || (is_int($id) && ($id == 1 || $id == 0)))) :
                    $errors[] = array('id' => $id, 'msg' => 'Segmentation type (id) only allows boolean or 0 or 1 values', 'val' => $val);
                continue;
                endif;
                $idVal = (int)$id;
            }

            //Llegamos aquí con los deberes hechos. Teemos que insertar para los usuarios en val, el dato id.
            //Si el action es bit o el datatypeid es simple, lo primero que hacemos, en vez de hacer update, borramos los datos y los volvemos a insertar
            if (!is_array($val)) {
                $errors[] = array('id' => $id, 'msg' => 'A list of idChannels must be provided', 'val' => $val);
                continue;
            }
            $field = ($dataActonType != 'bit') ? 'id_val' : 'val';
            foreach ($val as $idChannel) {
                $arrIns[] = ['id' => $idChannel, $field => $idVal];
                $arrInputIds[$idChannel] = $idChannel;
            }
            

            //DB::beginTransaction();
            //DB::rollback();
            //DB::commit();
           
            //Lo primero es buscar
        }
        if (!empty($arrIns)) {
            //Si no es vacío, procedemos a insertar
            //Si es bit o de tipo 1 borramos primero los datos

            DB::beginTransaction();
            if ($dataActonType == 'bit' || $dataTypeId == 1) {
                //borramos
                $this->__removeUsersFromTable($tableName, array_keys($arrInputIds));
            }
            $res = DB::connection('segmentation')->table($tableName)->insertIgnore($arrIns);

            DB::commit();

        }
        $this->errors = $errors;
        $this->processedItems = count($arrIns);
        $this->processedUsers = count($arrIns);
        //return $arrInputIds;
    }




    /**
     * Función auxiliar que gestiona la carga masiva cuando los datos vienen por id, en vez de por api/segmentation
     *
     * @return void
     */
    protected function createUsersByIdCRMData()
    {
        $originalRequest = json_decode($this->dataLoadObj->request, true);
        $data = $originalRequest['data'];
        $arrInputIds = [];
        $errors=[];

        foreach ($data as $datum) {
            $arrIns = [];
            $arrIdChannels = [];
            $idChannelArr = $datum['id'];
            $api_name = $datum['api_name'];
            $inputDataType = $datum['input_data_mode'];
            $val = $datum['val'];
            //Lo primero como siempre chequeamos la api_name, y cogemos su tipo, tablas, etc
            $data = AaaaTableControl::where('api_name', $api_name)->limit(1)->get();
            if (empty($data) || empty($data[0])) {
                $errors[] = array('id' => 0, 'msg' => 'Operation not allowed on the segmentation ' . $api_name, 'val' => '');
                $this->errors = $errors;
                return;
            }
            $tableName = $data[0]->name;

            $tableValsName = $tableName . $this->tablePostFix;
            /**
             * Tipos de datos que pueden existir en la tabla. 
             * 1::simple
             * 2::múltiple
             * 
             * Si es Simple (1) Tendremos que borrar los datos existentes y actualizar los nuevos, o actualizar directamente los existentes
             */
            $dataTypeId = $data[0]->data_type_id;
            /**
             * Acción de la tabla.
             * Pueden tener los valores de bit, ignore, normal (habrá más en el futuro).
             * Si es Ignore no se hace nada. Si es bit es único además.
             */
            $dataActonType = $data[0]->action;
            foreach ($val as $value) {
                if ($dataActonType != 'bit') {
                    $idVal = $this->__getValueId($tableValsName, $value, $inputDataType);
                    if ($idVal === false) {
                    //Ha habido un error. no ha encontrado el dato
                        $errors[] = array('id' => $id, 'msg' => 'ID Value not found', 'val' => $val);
                        continue;
                    }

                } else {
                    if (!(is_bool($value) || (is_int($value) && ($value == 1 || $value == 0)))) {
                        $errors[] = array('id' => $value, 'msg' => 'Segmentation type (id) only allows boolean or 0 or 1 values', 'val' => $val);
                        break;

                    }
                    $idVal = (int)$value;
                }
                foreach ($idChannelArr as $idChannel) {
                    # code...
                    $field = ($dataActonType != 'bit') ? 'id_val' : 'val';
                    $arrIns[] = ['id' => $idChannel, $field => $idVal];
                    if (empty($arrInputIds[$idChannel]))
                        $arrInputIds[$idChannel] = 0;
                    $arrInputIds[$idChannel]++;
                    $arrIdChannels[$idChannel] = $idChannel;
                    $this->processedItems ++;
                }
                if ($dataActonType == 'bit' || $dataTypeId == 1) {
                    //No recorremos más datos, porque ya tenemos lo que queremos, hacemos un break para salir del foreach
                    break;
                }

            }

            if (!empty($arrIns)) {
                //Si no es vacío, procedemos a insertar
                //Si es bit o de tipo 1 borramos primero los datos

                DB::beginTransaction();
                if ($dataActonType == 'bit' || $dataTypeId == 1) {
                    //borramos
                    $this->__removeUsersFromTable($tableName, array_keys($arrIdChannels));
                }
                $res = DB::connection('segmentation')->table($tableName)->insertIgnore($arrIns);

                DB::commit();

            }
           
            unset($arrIns);
            unset($arrIdChannels);


        }
        $this->errors = $errors;

        $this->processedUsers = count($arrInputIds);
       
    }


    /**
     * Función auxiliar. 
     * Dado una tabla de valores, un valor a buscar y un tipo, devolverá dependiendo de type:
     *      * type == id
     *          devolverá el id (es de entrada) si existe. Vacío o false si no se encuentra
     *       * type == crm , val
     *           devolverá el id encontrado. Si no se encuentra devuelve vacío o false.
     *
     * @param string $tableName Nombre de la tabla
     * @param variant $value Valor a buscar. Puede ser integer (id) o string (crm,val)
     * @param string $type Tipo de dato de entrada
     * @return variant integer/boolean. Integer si se encuentra, false si no se encuentra.
     */
    protected function __getValueId($tableName, $value, $type)
    {
        //Segçun el type buscamos el dato en dicha columna, devolviendo el id si se encuentra
        $field = 'id';
        if ($type == 'crm')
            $field = 'val_crm';
        elseif ($type == 'val')
            $field = 'val_normalized';


        $dat = DB::connection('segmentation')->table($tableName)->where($field, $value)->select('id')->get();
        if ($dat->count() < 1)
            return false;
        return $dat[0]->id;

    }


    /**
     * Función auxiliar que dado una tabla, borra los datos que están en el array de entrada
     * Esto se hace cuando los datos son únicos (tipo bit o dataype == simple)
     *
     * @param string $tableName Nombre de la tabla donde borrar los datos de los ids
     * @param array $idsArr Array con los usuarios
     * @return void
     */
    protected function __removeUsersFromTable($tableName, $idsArr)
    {
        DB::connection('segmentation')->table($tableName)->whereIn('id', $idsArr)->delete();
    }

    /**
     * Constructor de la clase
     */
    public function __construct(DataLoad $dataLoadObj)
    {
        $this->tablePostFix = config('api-crm.table_val_postfix');
        $this->dataLoadObj = $dataLoadObj;
        $functionality = $dataLoadObj->functionality;
        $result = $this->{$functionality}();

    }


    /**
     * Getter de los errores generados
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Getter de número de items procesados
     *
     * @return integer
     */
    public function getProcessedItems()
    {
        return $this->processedItems;
    }
/**
 * Función que devolverá información en arrays adicionales
 *
 * @return void
 */
    public function getProcessedUsers() {
        return $this->processedUsers;
    }
    
}