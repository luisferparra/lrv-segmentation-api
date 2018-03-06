<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RequestNewFieldTableControl;
use App\Http\Requests\Admin\RequestNewValueTables;
use App\Models\AaaaTableControl;
use App\Models\DataType;
use App\Api\SegmentationSchema;
use Schema;
use DB;

/**
 * Controlador
 */
class AdminDataController extends Controller
{
	//
	/**
	 * Controlador de Fields. Listado
	 * @author LFP
	 *
	 */


	protected $inEdition = false;
	protected $tablePostFix = '';

	protected $schema = null;


	public function __construct(SegmentationSchema $schema)
	{
	//parent::__construct();
		$this->schema = $schema;
		
		$this->schema->setAllowCreateAndRemove(true);


	}


	protected function __getTablePostfix()
	{

		if (empty($this->tablePostFix))
			$this->tablePostFix = $this->schema->getTablePostFix();
		return $this->tablePostFix;
	}

	public function fieldsIndex()
	{
		$a = AaaaTableControl::with('data_type')->get();

		return view('admin.fieldList', ['data' => $a]);
	}


	/**
	 * función que elimna una tabla del esquema segmentation
	 *
	 * @param string $tableName Nombre de la tabla a eliminar
	 * @return void
	 */
	protected function __tableRemove($tableName)
	{
		Schema::connection('segmentation')->dropIfExists($tableName);
	}

	/**
	 * Función que del esquema segmentation devuelve si la tabla existe
	 *
	 * @param string $tableName Nombre de la tabla a eliminar
	 * @return boolean
	 */
	protected function __tableExists($tableName)
	{
		return Schema::connection('segmentation')->hasTable($tableName);
	}




	/**
	 * Función de edición de registro de la tabla de control
	 *
	 * @param AaaaTableControl $tableControl Objeto a editar
	 * @return string retorna el formulario con los datos de entrada
	 */
	public function fieldsEdit(AaaaTableControl $tableControl)
	{
//Tenemos que buscar información de los datos en las tablas. Para ello cogemos el campo name, y vemos si existe la tabla, y si es así, realizamos counts.

		$tableName = $tableControl->name;
		$tableNameVals = $tableName . $this->__getTablePostfix();
		$totItems = 0;
		$totVals = 0;
		$labelItems = '# of Users with Information';
		$labelVals = '# of Values to be Segmented';
		if (!Schema::connection('segmentation')->hasTable($tableName)) {
			$labelItems = 'Warning, the table of Users NOT EXISTS';
		} else {
//Si existe la tabla, realizamos el conteo de datos
			$totItems = DB::connection('segmentation')->table($tableName)->count();
		}

		if (!Schema::connection('segmentation')->hasTable($tableNameVals)) {
			$labelVals = 'Warning, the table of Values Does NOT EXISTS';
		} else {
//Si existe la tabla, realizamos el conteo de datos
			$totVals = DB::connection('segmentation')->table($tableNameVals)->count();
		}
		$data_type = DataType::all();

		return view('admin.fieldForm', ['segmentationCount' => ['first' => ['num' => $totItems, 'label' => $labelItems], 'second' => ['num' => $totVals, 'label' => $labelVals]], 'data_types' => $data_type, 'tableControl' => $tableControl]);

	}

	/**
	 * Función de inserción de nuevo campos
	 * @return [type] [description]
	 */
	public function fieldsNew()
	{
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



	public function fieldsUpdate(RequestNewFieldTableControl $request, AaaaTableControl $tableControl)
	{
		$this->inEdition = true;
		$this->schema->setInEdition(true);
		$status = 'success';
		DB::beginTransaction();
		$name = str_replace('-', '_', (str_slug(strtolower($request->get('name')))));
		//$tableControl->name = $name;
		$tableControl->action = $request->get('action');
		$tableControl->description = ucwords(trim($request->get('description')));
		$tableControl->api_name = str_slug(trim($request->get('api_name')));
		$tableControl->data_type_id = $request->get('data_type_id');
		$tableControl->save();
		if (!$this->schema->postCreateTableSystem($name)) {
			$status = 'error';
			$msg = 'There is been an error creating fields. Check if tables already exists';

			DB::rollback();

		} else {
			$msg = sprintf('Item with #%s has been updated', $tableControl->id);


			DB::commit();


		}
		return redirect()->route('AdminFieldsIndex')->with('status', $status)->with('msg', $msg);

	}

	/**
	 * Función pública que inserta el registro nuevo
	 * @return [type] [description]
	 */
	public function fieldNewInsert(RequestNewFieldTableControl $request)
	{
		$status = 'success';

		DB::beginTransaction();
		$reg = new AaaaTableControl();
		$name = str_replace('-', '_', (str_slug(strtolower($request->get('name')))));
		$reg->name = $name;
		$reg->action = $request->get('action');
		$reg->description = ucwords(trim($request->get('description')));
		$reg->api_name = str_slug(trim($request->get('api_name')));
		$reg->data_type_id = $request->get('data_type_id');
		$reg->save();
		if (!$this->schema->postCreateTableSystem($name) || !$reg->id) {
			$status = 'error';
			$msg = 'There is been an error creating fields. Check if tables already exists';

			DB::rollback();

		} else {
			$msg = sprintf('Item with #%s has been created', $reg->id);


			DB::commit();


		}
		return redirect()->route('AdminFieldsIndex')->with('status', $status)->with('msg', $msg);

	}


	/**
	 * Función pública que borrará si se puede las tablas
	 *
	 * @param AaaaTableControl $tableControl
	 * @return void
	 */
	public function fieldsRemove(AaaaTableControl $tableControl)
	{


		$error = false;
		$status = "success";
		$msg = 'Item removed correctly';
		$tableName = $tableControl->name;
		$tableNameVals = $tableName . $this->__getTablePostfix();
		$countTable = $countTableVals = 0;

		$existsTable = $this->__tableExists($tableName);
		$existsTableVals = $this->__tableExists($tableNameVals);
		if ($existsTableVals) {
			$countTableVals = DB::connection('segmentation')->table($tableNameVals)->count();
			if ($countTableVals > 0) {
			//No puede borrarse
				$error = true;
				$status = "error";
				$msg = 'Table cannot be deleted because Segmentation Data has ' . $countTableVals . ' records';
			}

		}
		if (!$error && $existsTable) {
			$countTable = DB::connection('segmentation')->table($tableName)->count();
			if ($countTable > 0) {
	//No puede borrarse
				$error = true;
				$status = "error";
				$msg = 'Table cannot be deleted because Users Data has ' . $countTable . ' records';
			}
		}

		if (!$error) {
			if ($existsTable)
				$this->__tableRemove($tableName);
			if ($existsTableVals)
				$this->__tableRemove($tableNameVals);
			$tableControl->delete();
		}
		return redirect()->route('AdminFieldsIndex')->with('status', $status)->with('msg', $msg);


	}



	//#################################################################################
	//################################## Sección VALUES
	//##################################################################################
	/**
	 * Función de la sección del Admin que gestiona las tablas con valores.
	 * Devuelve el listado, dada una identificador de la tabla de control
	 *
	 * @param AaaaTableControl $tableControl Objeto principal de tabla
	 * @return Vista con valores. array con lao datos 'data', section con la sección (para epígrafe de admin)
	 */
	public function valuesIndex(AaaaTableControl $tableControl)
	{
		$table = $tableControl->name;
		$tableVals = $table . $this->__getTablePostfix();
		$contUsers = DB::connection('segmentation')->table($table)->count();
		$dat = DB::connection('segmentation')->table($tableVals)->get();

		return view('admin.valuesList', ['data' => $dat, 'cont' => $contUsers, 'tableControlId' => $tableControl->id])->with('section', $tableControl->description)->with('aaaa_table_controls_id', $tableControl->id)->with('tableName', $table);
	}


	public function valuesNew(AaaaTableControl $tableControl)
	{
		return view('admin.valuesForm', ['tableControlId' => $tableControl->id]);

		dd($tableControl);
	}

	public function valuesEdit(AaaaTableControl $tableControl, $valueId)
	{
		
		$table = $tableControl->name;
		$tableVals = $table . $this->__getTablePostfix();
		$data = DB::connection('segmentation')->table($tableVals)->find($valueId);
		if (is_null($data)) {
			return abort(404);
		}

		return view('admin.valuesForm', ['data' => $data, 'tableControlId' => $tableControl->id, 'valId' => $valueId]);
	}
/**
 * Fuincón pública que inserta un nuevo valor en una tabla referencial de valores
 *
 * @param RequestNewValueTables $request
 * @return void
 */
	public function valuesNewInsert(AaaaTableControl $tableControl,RequestNewValueTables $request)
	{
		$val_crm = trim($request->get('val_crm'));
		$val_normalized = trim($request->get('val_normalized'));
		//Cogemos de qué tabla se trata de $tableControl
		$table = $tableControl->name.config('api-crm.table_val_postfix');
		//Buscamos si existe el elemento... si no, devolvemos un error y fuera
		$exists = (DB::connection('segmentation')->table($table)->find($idValue)!==null);
		if (!$exists)
			return redirect()->route('AdminValuesIndex',$tableControl->id)->with('status', 'error')->with('msg', 'Value not found');
			//Chequeamos que no existtan ya los datos ya que deben ser únicos
		if (DB::connection('segmentation')->table($table)->where('val_crm',$val_crm)->orWhere('val_normalized',$val_normalized)->count()>0)
			return redirect()->route('AdminValuesIndex',$tableControl->id)->with('status', 'error')->with('msg', 'Both values must be unique');
			
		DB::connection('segmentation')->table($table)->insert(["val_crm"=>$val_crm,"val_normalized"=>$val_normalized]);
		return redirect()->route('AdminValuesIndex',$tableControl->id)->with('status', 'success')->with('msg', 'Data Inserted Correctly');
			
	}

	public function valuesEditPost(AaaaTableControl $tableControl,$idValue,RequestNewValueTables $request) {
		
		$val_crm = trim($request->get('val_crm'));
		$val_normalized = trim($request->get('val_normalized'));
		//Cogemos de qué tabla se trata de $tableControl
		$table = $tableControl->name.config('api-crm.table_val_postfix');
		//Buscamos si existe el elemento... si no, devolvemos un error y fuera
		$exists = (DB::connection('segmentation')->table($table)->find($idValue)!==null);
		if (!$exists)
			return redirect()->route('AdminValuesIndex',$tableControl->id)->with('status', 'error')->with('msg', 'Value not found');
		DB::connection('segmentation')->table($table)->where('id',$idValue)->update(["val_crm"=>$val_crm,"val_normalized"=>$val_normalized]);
		return redirect()->route('AdminValuesIndex',$tableControl->id)->with('status', 'success')->with('msg', 'Data Updated Correctly');

		
		dd($request);
	}
}
