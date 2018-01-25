<?php

namespace App\Http\Controllers;

use App\Models\AaaaTableControl;

class SegmentationController extends Controller {
	//
	//
	/**
	 * Función de Api que devuelve el listado de Columnas a segmentar
	 *
	 *
	 * @return json Json con la información de las columnas
	 */
	public function showInfo() {
		//\DB::listen(function ($sql) {
		//	var_dump($sql->sql, $sql->bindings);
		//});
		$a = AaaaTableControl::with('data_type')->where('action', '<>', 'ignore')->get();
		$out = [];
		foreach ($a as $k => $val) {
			# code...
			$item = array('description' => $val->description, 'api_name' => $val->api_name, 'data_type' => $val->data_type->name);
			$out[] = $item;
			unset($item);
		}
		return ($out);
	}
}
