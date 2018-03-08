<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Schema;
use DB;
use App\Models\Statistic;
use App\Models\StatisticsDisplay;
use App\Models\StatisticsType;
use App\Admin\Stats\GetStat;

class AdminHomeController extends Controller
{
	/**
	 * Arrayquecontiene el número máximo de elementos según el campo statisctis_displays_id
	 */
	const itemsTop = [1 => 4];

/**
 * ruta home de entrada al site
 *
 * @return void
 */
public function home() {
	return view('welcome');
}

	/**
	 * 
	 * DEPRECATED DEPRECATED DEPRECATED
	 * Función que devuelve un array con los datos para mostrar en el Dashboard
	 * Estará dentro de poco Deprecated
	 *
	 * @return array key=>value de los datos encontrados
	 */
	protected function __get_CountersDashboard()
	{
		$users = DB::connection('segmentation')->table('bbdd_subscribers')->count();
		$openers = DB::connection('segmentation')->table('marketing_openers')->where('val', true)->count();
		$clickers = DB::connection('segmentation')->table('MARKETING_CLICKER')->where('val', true)->count();
		$purchasers = DB::connection('segmentation')->table('MARKETING_PURCHASER')->where('val', true)->count();
		return array('users' => $users, 'openers' => $openers, 'clickers' => $clickers, 'purchasers' => $purchasers);
	}

	/**
	 * Función que coge las estadísticas a mostrar de cierto tipo y en cierto lugar
	 *
	 * @param integer $type tipo de estadísticas. Tabla statistics_types
	 * @param integer $display Lugar de display Tabla statistics_displays
	 * @return array Array Con los datos
	 */
	protected function __getStatistics($type, $display)
	{
/* 		\DB::listen(function ($sql) {
			var_dump($sql->sql);
		}); */
		$data = Statistic::where('active', true)->where('statistics_types_id', $type)->where('statistics_displays_id', $display)->orderBy('order')->get();
		$statisticsTypesArr = StatisticsType::find($type);
		$originalLayout = trim($statisticsTypesArr->layout);
		$originalType = trim($statisticsTypesArr->type);
		//$statisticsDisplaysArr = StatisticsDisplay::find($display);
		$out = [];

		$counter = 1;
		$items = [];
		foreach ($data as $datum) {
			$label = trim($datum->label);
			$data = trim($datum->data);
			$colour = trim($datum->colour);
			$icon = trim($datum->icon);
			$id = $datum->id;
			switch ($originalType) {
				case 'number':
					$currLayout = str_replace(['[[COLOR]]', '[[ICON]]', '[[LABEL]]', '[[NUMBER]]'], [$colour, $icon, $label, number($data)], $originalLayout);
					$items[] = $currLayout;
					break;
				case 'graph-line':
				case 'graph-column':
				case 'graph-donut':
				case 'graph-pie':
				case 'graph-percentage':
				case 'graph-bar':
				$colours =   ['light-blue', 'blue', 'violet', 'red','orange', 'yellow', 'green', 'light-green', 'purple', 'magenta', 'grey', 'dark-grey'];
					$tmpData = json_decode($data, true);
					$item = [];
					$item['name'] = 'graph' . $id;
					$item['label'] = $label;
					$item['type'] = str_replace('graph-','',$originalType);//str_replace('graph-','',$originalType);
					$item['colour'] = "'".$colours[array_rand($colours)]."'";
					$tmp = is_array($tmpData['chartData']) ? $tmpData['chartData'] : json_decode($tmpData['chartData'], true);
					$t =array_keys($tmp);
					array_walk($t, function(&$item) { $item = "'".$item."'"; });
					$item['graphLabel'] = implode(',',$t);             
					$item['graphData'] = implode(',',array_values($tmp));
					$items[] =$item;
					break;
				default:
					# code...
					break;
			}


			$counter++;
			if ($counter > self::itemsTop) {
				$out[] = $items;
				$counter = 1;
				unset($items);
				$items = [];
			}
		}
		if (count($items) > 0)
			$out[] = $items;
		return $out;
	}
	/**
	 * Función del dashboard del admin
	 * @return [type] [description]
	 */
	public function index()
	{
		/* $statCharts = new GetStat();
		$statCharts->getStatsFromId(11); */
		$statisticsDisplaysArr = StatisticsDisplay::find(1);
		$displayType = trim($statisticsDisplaysArr->displayed_at);
		//type,display
		$numberDashboardTop = $this->__getStatistics(1, 1);
		$charts = $this->__getStatistics(2, 1);
		$charts2 = $this->__getStatistics(5,1);
		$charts = array_merge($charts,$charts2);
		$tmp = '{
			title: "Some Data",
			values: [25, 40, 30, 35, 8, 52, 17, -4]
		  }';
		//$countersArr = $this->__get_CountersDashboard();
		//dd($countersArr);
		return view('admin.dashboard', [$displayType => $numberDashboardTop, 'charts'=>$charts,'chart' => $tmp]);
	}
}
