<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Schema;
use DB;

class AdminHomeController extends Controller {
	//
/**
 * Función que devuelve un array con los datos para mostrar en el Dashboard
 *
 * @return array key=>value de los datos encontrados
 */
protected function __get_CountersDashboard() {
	$users = DB::connection('segmentation')->table('bbdd_subscribers')->count();
	$openers = DB::connection('segmentation')->table('marketing_openers')->where('val',true)->count();
	$clickers = DB::connection('segmentation')->table('MARKETING_CLICKER')->where('val',true)->count();
	$purchasers = DB::connection('segmentation')->table('MARKETING_PURCHASER')->where('val',true)->count();
	return array('users'=>$users,'openers'=>$openers,'clickers'=>$clickers,'purchasers'=>$purchasers);
}
	/**
	 * Función del dashboard del admin
	 * @return [type] [description]
	 */
	public function index() {

		
		$countersArr = $this->__get_CountersDashboard();
		//dd($countersArr);
		return view('admin.dashboard',['counters'=>$countersArr]);
	}
}
