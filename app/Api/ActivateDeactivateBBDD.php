<?php

/**
 * 
 * Clase que controla la acción de activar/desactivar bbdds
 * 
 * Desactivar: el proceso cogerá todos los usuarios asociados a dicha bbdd y los insertará en.bbdd_users_bulk_unsubs borrándolos luego de la tabla principal
 * Activar: el proceso contrario. Cogerá todos los usuarios asociados a la bbdd de la tabla bbdd_users_buklk_unsubs y los insertará en la tabla principal
 * 
 */



namespace App\Api;


use DB;
use Schema;
use App\Models\AaaaTableControl;

class ActivateDeactivateBBDD
{
    protected $request = null;
    /**
     * Acción: activate|deactivate
     *
     * @var string
     */
    protected $action = '';
    /**
     * Identificador de bbdd a activar/desactivar
     *
     * @var string
     */
    protected $idBbdd = '';

    /**
     * Esquema temporal nombre
     *
     * @var string
     */
    protected $schemaTemp = '';
    /**
     * Esquema temporal
     *
     * @var string
     */
    protected $schema = '';

    protected $tableTemporal = '';



    /**
     * Funcionalidad para activar la bbdd correspondiente
     */
    protected function activate()
    {
        $this->createTemporaryTableIdChannels('temp');

        $q = "INSERT IGNORE INTO bbdd_users SELECT id,id_val FROM `" . $this->schemaTemp . "`.`bbdd_users` WHERE id_val=" . $this->idBbdd;

        DB::connection('segmentation')->getpdo()->exec($q);
        
//Insertamos también en la tabla temporal
        $q = "INSERT IGNORE INTO `" . $this->tableTemporal . "`  SELECT id FROM `bbdd_users` WHERE id_val=" . $this->idBbdd;
        DB::connection('temp')->getpdo()->exec($q);

//Cogemos las apis y sus tablas
        $aaTableCollection = $this->getTableControlData();
        foreach ($aaTableCollection as $key => $value) {
            $name = trim($value->name);
            if (Schema::connection('temp')->hasTable($name)) {
                //Si está duplicado no hacemos nada... ya que entendemos que los datos existentes son más modernos que los que no existen
                $q = "INSERT INTO `" . $name . "` (id,id_val) (SELECT tbla.id, tbla.id_val FROM `" . $this->schemaTemp . "`.`" . $this->tableTemporal . "` tmp INNER JOIN `" . $this->schemaTemp . "`.`" . $name . "` tbla ON tmp.id=tbla.id) ON DUPLICATE KEY UPDATE `" . $name . "`.id=`" . $name . "`.id";
               
                DB::connection('segmentation')->getpdo()->exec($q);
                $q = "DELETE FROM `" . $name . "` WHERE id IN (SELECT id FROM `" . $this->tableTemporal . "`)";
                DB::connection('temp')->getpdo()->exec($q);
            }

        }
//Borramos de la tabla secundaria
        DB::connection('temp')->table('bbdd_users')->where('id_val', $this->idBbdd)->delete();
        $this->dropTemporaryTable('temp');
    }
    /**
     * Funcionalidad que desactiva una bbdd dada
     *
     * @return void
     */
    protected function deactivate()
    {
        $this->createTemporaryTableIdChannels('segmentation');
        $q = "INSERT IGNORE INTO bbdd_users (id,id_val) SELECT id,id_val FROM `" . $this->schema . "`.`bbdd_users` WHERE id_val=" . $this->idBbdd;

        DB::connection('temp')->getpdo()->exec($q);
       
        //Insertamos también en la tabla temporal
        $q = "INSERT IGNORE INTO `" . $this->tableTemporal . "`  SELECT id FROM `bbdd_users` WHERE id_val=" . $this->idBbdd;
        DB::connection('segmentation')->getpdo()->exec($q);
         //Ahora sacamos los datos que no existen más en la tabla, es decir, que eran únicos de esa bbdd
        $q = "DELETE FROM `" . $this->tableTemporal . "` WHERE id IN (SELECT id FROM `bbdd_users` WHERE id_val!=" . $this->idBbdd . ")";
        DB::connection('segmentation')->getpdo()->exec($q);
       
       
//Ahroa en la tabla temporal solo tenemos los que eran únicos, y que tenemos que pasar sus datos a las temporales   
        $aaTableCollection = $this->getTableControlData();
        foreach ($aaTableCollection as $key => $value) {
            $name = trim($value->name);
            if (Schema::connection('temp')->hasTable($name)) {
                $q = "INSERT INTO `" . $name . "` (id,id_val) (SELECT tbla.id, tbla.id_val FROM `" . $this->schema . "`.`" . $this->tableTemporal . "` tmp INNER JOIN `" . $this->schema . "`.`" . $name . "` tbla ON tmp.id=tbla.id) ON DUPLICATE KEY UPDATE id_val=VALUES(id_val)";

                DB::connection('temp')->getpdo()->exec($q);

            }
            $q = "DELETE FROM `" . $name . "` WHERE id IN (SELECT id FROM `" . $this->tableTemporal . "`)";
            DB::connection('segmentation')->getpdo()->exec($q);
        }
         //Borramos los datos de la principal finalmente. No lo podemos hacer antes por las constraints
        DB::connection('segmentation')->table('bbdd_users')->where('id_val', $this->idBbdd)->delete();
        $this->dropTemporaryTable('segmentation');


    }


    /**
     * Función que crea una tabla temporal solo con el campo id (id channel) para ser utilizado en la carga final (load data infile) antes de sacar todos los datos finales
     *
     * @return void
     */
    protected function createTemporaryTableIdChannels($schema)
    {
        $table = md5(rand());
        Schema::connection($schema)->create($table, function ($table) {
            $table->integer('id')->unasigned()->primary();

        });
        $this->tableTemporal = $table;
    }

    /**
     * Función que simplemente elimina si existe una tabla temporal creada
     *
     * @param string $table
     * @return void
     */
    protected function dropTemporaryTable($schema)
    {
        Schema::connection($schema)->dropIfExists($this->tableTemporal);
    }

    /**
     * Devuelve información sobre las distintas segmentaciones existentes
     *
     * @return void
     */
    protected function getTableControlData()
    {
        return AaaaTableControl::where('action', '!=', 'ignore')->get();
    }

    public function __construct($request)
    {
        $this->request = $request;
        $this->action = $request['action'];
        $this->idBbdd = $request['idBbdd'];
        $this->schemaTemp = config('database.connections.temp.database');
        $this->schema = config('database.connections.segmentation.database');

        $this->{$this->action}();

    }

} 