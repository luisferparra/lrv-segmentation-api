<?php

/**
 * Clase que controla lo relacionado con el esquema de Segmentación donde tenemos todas las tablas de segmentación y no tenemos modelo
 * @author LFP <luisfer.parra@netsales.es>
 */


namespace App\Api;

use Schema;
use DB;

class SegmentationSchema
{
    /**
     * Variable que definirá el postfix de las tablas de valores
     *
     * @var string
     */
    protected $tablePostFix = '';
    /**
     * Variable que nos dice si estamos en el admin editando
     *
     * @var boolean
     */
    protected $inEdition = false;

    /**
     * Variable que nos dice si el usuario está permitido para crear y borrar tablas
     *
     * @var boolean
     */
    protected $allowCreateAndRemove = false;

    public function __construct($config)
    {

        $this->tablePostFix = $config;
    }

    /**
     * Getter que devuelve el postfix de las tablas
     *
     * @return string
     */
    public function getTablePostFix()
    {
        return $this->tablePostFix;
    }
    /**
     * Setter de la variable inEdition, que dice si se está en estado de edición
     *
     * @param boolean $inEdition

     */
    public function setInEdition($inEdition)
    {
        $this->inEdition = $inEdition;
    }
    /**
     * Setter que dirá si se permite crear y borrar
     *
     * @param [type] $allowCreateAndRemove
     * @return void
     */
    public function setAllowCreateAndRemove($allowCreateAndRemove)
    {
        $this->allowCreateAndRemove = $allowCreateAndRemove;
    }



    /**
     * Función que del esquema segmentation devuelve si la tabla existe
     *
     * @param string $tableName Nombre de la tabla a eliminar
     * @return boolean
     */
    protected function getTableExists($tableName)
    {
        return Schema::connection('segmentation')->hasTable($tableName);
    }


    /**
     * Función que crea el esquema de segmentación de tablas, si es un bit
     *
     * @param $tableName $tableName Nombre de la tabla a Crear
     * @return boolean true
     */
    protected function __postCreateTableSystem_Bit($tableName)
    {
        Schema::connection('segmentation')->create($tableName, function ($table) {
            $table->integer('id')->unasigned()->primary();
            $table->boolean('id_val')->index();

            //$table->primary(['id']);
            $table->foreign('id')->references('id')->on('bbdd_users');

        });
        Schema::connection('temp')->create($tableName, function ($table) {
            $table->integer('id')->unasigned()->primary();
            $table->boolean('id_val')->index();
            //$table->integer('bbdd_id')->unasigned->index();

            //$table->primary(['id']);
            

        });
        //Creamos la temporal
        return true;
    }



    /** 
     * Función que dado un nombre de una tabla, crea todo el sistema de latabla.
     * Crea 2 tablas: una que tiene los valores, otra que tiene los datos de usuarios
     * @param  String $tableName Nombre de la tabla Principal a crear
     * @param boolean $isBitTable false por defecto. Si es true, la tabla es de tipo bit, por lo que no habrá que crear la de vals
     * @return Boolean   Si se ha creado todo correcxtamente
     */
    public function postCreateTableSystem($tableName, $isBitTable = false)
    {
        if (!$this->allowCreateAndRemove)
            return false;

        $tableNameVals = $tableName . $this->tablePostFix;
        $existsTable = $this->getTableExists($tableName);
        $existsTableVals = $this->getTableExists($tableNameVals);
        if ($this->inEdition && $isBitTable && $existsTable)
            return true;
        elseif (!$this->inEdition && $existsTable && $isBitTable)
            return false;
        elseif (($this->inEdition && $existsTable && $existsTableVals))
            return true;
        elseif (!$this->inEdition && ($existsTable || $existsTableVals)) {

            return false;

        }
        if ($isBitTable) {
            return $this->__postCreateTableSystem_Bit($tableName);
        }

        if (!$this->inEdition || ($this->inEdition && !$existsTableVals)) {
            Schema::connection('segmentation')->create($tableNameVals, function ($table) {
                $table->increments('id');
                $table->string('val_crm')->unique();
                $table->string('val_normalized')->unique();

            });
        }
        if (!$this->inEdition || ($this->inEdition && !$existsTable)) {

            Schema::connection('segmentation')->create($tableName, function ($table) use ($tableNameVals) {
                $table->integer('id')->unasigned();
                $table->integer('id_val')->index();

                $table->primary(['id', 'id_val']);
                $table->foreign('id')->references('id')->on('bbdd_users');
                $table->foreign('id_val')->references('id')->on($tableNameVals);


            });
            //creamos también su réplica en temporal
            Schema::connection('temp')->create($tableName, function ($table){
                $table->integer('id')->unasigned();
                $table->integer('id_val')->index();
                
                $table->primary(['id', 'id_val']);
                


            });
        }
        return true;
    }

}