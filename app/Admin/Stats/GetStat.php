<?php

/**
 * Clase que Devuelve los datos necesarios para pintar una gráfica pre-generada
 */

namespace App\Admin\Stats;

use App\Models\Statistic;
use App\Models\AaaaTableControl;

class GetStat
{

    /**
     * Función pública. Devuelve dada una posición de página, todas las estadísticas asociadas a dicha página
     *
     * @return return
     */
    public function getStatsFromPage()
    {

    }

    /**
     * Funcióin pública que devuelve estadísticas según página y posición
     *
     * @return void
     */
    public function getStatsFromPagePosition()
    {

    }

    /**
     * Función pública que devuelve estadísticas asociadas a una api
     * 
     * @param string $api_name Name of the Api to search for
     *
     * @return void
     */
    public function getStatsFromApiName($api_name)
    {
        $data = Statistic::where('api_name',$api_name);
        if ($data===null) {
            return false;
        }
    }

    public function getStatsFromApiId($api_id)
    {
        $data = Statistic::where('api_nane',$api_name);
        if ($data===null) {
            return false;
        }
    }

    /**
     * Función privada que dado un identificador, devuelve las estadísticas asociadas
     *
     * @return void
     */
    public function getStatsFromId($id)
    {
        $data = Statistic::find($id);
        if ($data==null) {
            return false;
        }
        dd($data);
    }


    public function __construct()
    {

    }

    public function __destruct()
    {

    }
}