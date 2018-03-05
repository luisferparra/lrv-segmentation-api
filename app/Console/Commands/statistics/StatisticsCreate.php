<?php

/**
 * Comando que genera diariamente las estadísticas que se presentan en  distintos puntos del admin
 */
namespace App\Console\Commands\statistics;

use Illuminate\Console\Command;
use DB;
use Schema;
use App\Models\AaaaTableControl;
use App\Models\Statistic;
use Carbon\Carbon;

class StatisticsCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:home:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will create statistics displayed at the home';
    /**
     * Variable privada.Devuelve el número de elemenntos máximos por defecto al crear una estadística
     *
     * @var integer
     */
    private $numElementsStatsDefault = 30;

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
        $statisticsArr = Statistic::whereIn('statistics_displays_id', [1, 2])->where('active', true)->get();
        foreach ($statisticsArr as $statistic) {
            $table = $statistic->aaaa_table_control->name;
            $statisticId = $statistic->id;
            $statisticType = $statistic->statistics_types_id;
            $tableType = $statistic->aaaa_table_control->action;
            $originalData = trim($statistic->data);
            $lastUpdate = trim($statistic->updated_at);
            switch ($statisticType) {
                case 1:
                //Numeric, solo es conteo
                    $obj = DB::connection('segmentation')->table($table);
                    if ($tableType == 'bit')
                        $cont = $obj->where('id_val', 1)->count();
                        elseif ($tableType=='ignore') {
                            $cont = $obj->distinct('id')->count('id');
                        }
                    
                    $save = Statistic::find($statisticId);
                    $save->data = $cont;
                    $save->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                    $save->save();
                    # code...
                    break;
                case 2:
                case 3:
                    /**
                     * Lostipos 2 y 3 son de estadísticas.
                     * Tipo 2: Gráfica Lineal
                     * Tipo 3: Gráfica Donut
                     */
                    if (empty($originalData)) {
                        $data = ['maxItems' => $this->numElementsStatsDefault, 'periodRenew' => 1, 'chartData' => []];
                    } else $data = json_decode($originalData, true);
                    $chartData = json_decode($data['chartData'], true);
                    $maxElements = $data['maxItems'];
                    $periodRenew = $data['periodRenew'];
                    if (empty($lastUpdate) || $this->___diffDays($lastUpdate) >= $periodRenew) {
                        if (count($chartData) >= $maxElements) {
                            array_shift($chartData);
                        }
                        $cont = DB::connection('segmentation')->table($table)->count();

                        $chartData[Carbon::now()->format('Y-m-d')] = number($cont);
                        $data['chartData'] = json_encode($chartData);
                        $save = Statistic::find($statisticId);
                        $save->data = json_encode($data);
                        $save->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                        $save->save();
                    }

                    break;

                default:
                    # code...
                    break;
            }


        }

    }

    /**
     * Función privada que devuelve la diferencia de días entre una fecha pasada y hoy
     *
     * @param date $date
     * @return integer
     */
    private function ___diffDays($date)
    {
        $now = time();
        $your_date = strtotime($date);
        $datediff = $now - $your_date;

        return round($datediff / (60 * 60 * 24));
    }
}
