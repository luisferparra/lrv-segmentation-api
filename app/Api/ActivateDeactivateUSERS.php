<?php


/**
 * Clase que activará o desactivará usuarios de una o varias BBDDs
 * La versión 1 viene dada por act/desactivación de usuarios (idchannels) de una base de datos específica. La clase se ampliará a otras modalidades
 */

namespace App\Api;


use DB;
use Schema;
use App\Models\AaaaTableControl;
use Carbon\Carbon;

class ActivateDeactivateUSERS
{

    /**
     * tipo de acción
     * en la V1: byBBDD
     *
     * @var string
     */
    protected $actionType = '';
    /**
     * Tipo de acción.Activate/Deactivate
     *
     * @var string
     */
    protected $action = '';
    /**
     * BBDD donde se dará de baja al usuario
     *
     * @var integer
     */
    protected $idBbdd = 0;
    /**
     * REquest original
     *
     * @var array
     */
    protected $request = null;



    /**
     * Función que registra (insert ignore) a los usuarios en una bbdd específica
     *
     * @param array $usersList listado de usuarios
     * @return void
     */
    protected function registerByDB($usersList)
    {
        $arrIns = [];
        if (!empty($usersList)) {
            
            foreach ($usersList as $v) {
    # code...
                $arrIns[] = ['id' => $v, 'id_val' => $this->idBbdd];
            }
            DB::connection('segmentation')->table('bbdd_users')->insertIgnore($arrIns);

        }
    }


    /**
     * Función que dado un array de usuarios, los eliminaremos de las tablas de segmentación
     *
     * @param array $arrToRemoveUsers listado de idchannels a borrar
     * @return void
     */
    protected function removeUsersFromSegmentation($arrToRemoveUsers)
    {
        $tableControls = AaaaTableControl::where('action', '!=', 'ignore')->get();
        foreach ($tableControls as $k => $v) {
            $name = trim($v->name);
            DB::connection('segmentation')->table($name)->whereIn('id', $arrToRemoveUsers)->delete();
            # code...
        }
    }

    /**
     * Función que coge de $this->idBbdd la bbdd a dar de baja, y como entrada tiene los usuarios a los que dar de baja.
     * El algoritmo seráel siguiente. 
     *      Para cada usuario buscaremos si antes de borrarlo si es único para esa BBDD
     *      Si existen más, le borramosdirectamten y pasamos al siguiente
     *      Si es único, deberemos borrarlo de todas las demás tablas auxiliares de segmentación
     *      Por último, borrar al susuario de la tabla de usuarios
     * @param array $usersList listado de usuarios
     * @return void
     */
    protected function unsubByDB($usersList)
    {
        /**
         * Array que guardaremos los idchannels que antes de ser borrados hay que borrar su presencia en las tablas auxiliares de segmentaci´no
         */
        $arrToRemoveUsers = [];
        /**
         * Array con inserts para log históirico
         */
        $arrRemovedUsersData = [];
        
        foreach ($usersList as $idChannel) {
            $arrRemovedUsersData[] = ['id'=>$idChannel,'id_val'=>$this->idBbdd];
            $searchUser = DB::connection('segmentation')->table('bbdd_users')->where('id', $idChannel)->where('id_val', '!=', $this->idBbdd)->count();
            if ($searchUser == 0) {
                $arrToRemoveUsers[] = $idChannel;
            } else {

                DB::connection('segmentation')->table('bbdd_users')->where('id', $idChannel)->where('id_val', $this->idBbdd)->delete();
            }

        }
        if (count($arrToRemoveUsers) > 0) {
            $this->removeUsersFromSegmentation($arrToRemoveUsers);
                //Ahora borramos de la tabla principal
    //El último where no sería necesario ya que el usuario es único. Lo ponemos para asegurarnos que se borra a quien se dice
            DB::connection('segmentation')->table('bbdd_users')->whereIn('id', $arrToRemoveUsers)->where('id_val', $this->idBbdd)->delete();
        }

      /*   \DB::listen(function($sql) {
            print_r($sql->sql);
        }); */
        //Insertamos en el log la baja realizada junto con la fecha
  
        DB::connection('temp')->table('bbdd_users_historic_unsubs')->insertOnDuplicateKey($arrRemovedUsersData,[DB::raw("`updated_at`='".Carbon::now()->format('Y-m-d H:i:s')."'")]);

    }


    public function __construct($request)
    {

        $this->actionType = (empty($request['type'])) ? 'byBBDD' : trim($request['type']);
        $this->action = strtolower(trim($request['action']));
        $this->idBbdd = (!empty($request['idBbdd'])) ? $request['idBbdd'] : 0;
        $this->request = $request['requested'];
        switch ($this->action) {
            case 'register':
                switch ($this->actionType) {
                    case 'byBBDD':
                    # code...
                        return $this->registerByDB($this->request['data']);
                        break;

                    default:
                    # code...
                        break;
                }
                break;
            case 'unsub':
                switch ($this->actionType) {
                    case 'byBBDD':
                        # code...
                        return $this->unsubByDB($this->request['data']);
                        break;

                    default:
                        # code...
                        break;
                }
                break;
            default:
                # code...
                break;
        }

    }

}