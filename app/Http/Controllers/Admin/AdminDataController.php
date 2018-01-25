<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RequestNewFieldTableControl;
use App\Models\AaaaTableControl;
use App\Models\DataType;
use Schema;

/**
 * Controlador
 */
class AdminDataController extends Controller {
	//
	/**
	 * Controlador de Fields. Listado
	 * @author LFP
	 *
	 */
	public function fieldsIndex() {
		$a = AaaaTableControl::with('data_type')->get();

		return view('admin.fieldList', ['data' => $a]);
	}
/**
 * Función que dado un nombre de una tabla, crea todo el sistema de latabla.
 * Crea 2 tablas: una que tiene los valores, otra que tiene los datos de usuarios
 * @param  String $tableName Nombre de la tabla a crear
 * @return Boolean   Si se ha creado todo correcxtamente
 */
	protected function _createTableSystem($tableName) {
		$tableNameVals = $tableName . '_val';

		if (Schema::connection('segmentation')->hasTable($tableNameVals) || Schema::connection('segmentation')->hasTable($tableName)) {
			return false;
		}

		Schema::connection('segmentation')->create($tableNameVals, function ($table) use ($tableNameVals) {
			$table->increments('id');
			$table->string('val_crm')->key();
			$table->string('val_normalized')->key();
		});
		Schema::connection('segmentation')->create($tableName, function ($table) use ($tableNameVals) {
			$table->integer('id');
			$table->integer('id_val')->references('id')->on($tableNameVals);

			$table->primary(['id', 'id_val']);
		});
		return true;
	}

/**
 * Función de inserción de nuevo campos
 * @return [type] [description]
 */
	public function fieldsNew() {
		$tot = 0;
		$tmp = array();
		$contTables = AaaaTableControl::with('data_type')->where('action', '<>', 'ignore')->select([\DB::raw('count(*) as total'), 'data_type_id'])->groupBy('data_type_id')->get();

		foreach ($contTables as $key => $value) {
			# code...
			$tot = $tot + $value->total;

			$tmp[] = ucwords($value->data_type->name) . ": " . $value->total;
		}

		//cogremos ahora los referenciales que irán en el select
		$data_type = DataType::all();
		//$contUsers = DB::connection('segmentation')->table('email-domains')->count();
		//$contItems = DB::connection('segmentation')->table('email-domains-vals')->count();
		return view('admin.fieldForm', ['tableCount' => ['first' => ['num' => $tot, 'label' => 'Number of Segmentations'], 'second' => ['num' => implode(', ', $tmp), 'label' => 'Number of segmentations by data type']], 'data_types' => $data_type]);
	}
/**
 * Función pública que inserta el registro nuevo
 * @return [type] [description]
 */
	public function fieldNewInsert(RequestNewFieldTableControl $request) {
		$reg = new AaaaTableControl();
		$name = str_replace('-', '_', (str_slug(strtolower($request->get('name')))));
		$reg->name = $name;
		$reg->action = $request->get('action');
		$reg->description = ucwords(trim($request->get('description')));
		$reg->api_name = str_slug(trim($request->get('api_name')));
		$reg->data_type_id = $request->get('data_type_id');
		$reg->save();
		$this->_createTableSystem($name);
		return redirect()->route('AdminFieldsIndex')->with('status', 'success')->with('msg', 'El registro #' . $reg->id . ' ha sido insertado');

	}
}
