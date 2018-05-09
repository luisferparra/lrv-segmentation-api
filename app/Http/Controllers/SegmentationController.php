<?php

namespace App\Http\Controllers;

use DB;
use Redis;
use Auth;
use Webpatser\Uuid\Uuid;
use App\Api\Error\Error;
use App\Models\DataLoad;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Api\SegmentationSchema;
use App\Api\Error\ErrorResponse;
use App\Jobs\ProcessDataLoadJob;
use App\Jobs\ProcessActivateDeactivateJob;
use App\Api\SegmentationCounter;
use App\Models\AaaaTableControl;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Models\SegmentationCounterRequest;

use App\Http\Requests\CounterInputRequest;
use App\Http\Requests\ApiNameInputValidation;
use App\Http\Resources\SegmentationInfoResource;
use App\Http\Requests\ApiLoadInfoUuidValidation;
use App\Http\Requests\CreateUsersByIdApiRequest;

use App\Http\Requests\CreateUsersByNameApiRequest;
use App\Http\Resources\SegmentationInfoValuesResource;
use App\Http\Requests\Admin\RequestNewFieldTableControl;
use App\Api\SegmentationCounterInterface;
use App\Http\Requests\DataBasePostInputRequest;
use App\Http\Requests\DataBasePutActivateDeactivateInputRequest;
use App\Api\ActivateDeactivateBBDD;
use App\Http\Requests\UsersByDBInputRequest;
use App\Jobs\ProcessActivateDeactivateUsersFromDBJob;

/**
 * @SWG\Swagger(
 *   basePath="/api",
 * 		produces={"application/json"},
 * 		consumes={"application/json"},

 *   @SWG\Info(
 *     title="CRM API",
 * description = "Api CRM Segmentation of IdChannels. It nevers return personal data, always meta-data",
 *     version="1.1.0",
 * 	@SWG\Contact(name="Luis Fernando Parra", url="https://www.netsales.es"),
 *   )
 * )
 *   @SWG\ExternalDocumentation(
 *     description="Find out how to implement a call to the api using PHP and Curl",
 *     url="https://dev.netsales.es/stat-server/swagger/manual/"
 *   )
 * @SWG\Tag(
 *   name="Segmentation Info",
 *   description="Access to Information and update information about the posibility of segmentation",
 * 
 * )
 * 
 * @SWG\Tag(
 *   name="Synchro Data",
 *   description="Api for synchro CRM data with this system"
 * )
  * @SWG\Tag(
 *   name="Referential-Segmentation Data",
 *   description="Api for synchro CRM data (Segmentation or Referential Data)"
 * )
 * 
 * @SWG\Tag(
 *   name="Segmentation Operations",
 *   description="Api for Segmentate data"
 * )
 * 
 *  * @SWG\Tag(
 *   name="Users",
 *   description="Api for Users Load and Unsub"
 * )
 * *  * @SWG\Tag(
 *   name="Data Base",
 *   description="Api for Managing Data Bases"
 * )
  * @SWG\SecurityScheme(
 *   securityDefinition="passport",
 *   type="oauth2",
 *   tokenUrl="/api/login",
 *   flow="password",
 *   scopes={}
 * )
 *  *     @SWG\SecurityScheme(
 *          securityDefinition="token",
 * description="Please, copy the token returned in the header, and add Bearer before the token",
 *          type="apiKey",
 *          in="header",
 *          name="Authorization"
 *      )
 */
class SegmentationController extends Controller
{





	//
	//
	/**
	 * @SWG\Get(
	 *   path="/segmentations",
	 *   summary="Returns all possible segmentation available in the system",
	 * tags={"Segmentation Info"},
	  *   security={
 *     {"token": {}},
 *   },
	 *     description="Api get that returns all possibe segmentation available in the system. Inbetween all information, notice the api_name response, wich is the key for making segmentation all getting all available values for such segmentation",
	 *     produces={ "application/json"},
	 * @SWG\Response(
	 *         response=200,
	 *         description="List of all Segmentations Available",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoResponses")
	 * 
	 
	 *        
	 *     )
	 * )
	 * @SWG\Definition(definition="SegmentationInfoResponses", type="object", required={"data"},
	 * @SWG\Property(
	 *             property="data",
	 *             type="array",
	 *             format="int32",
	 * @SWG\Items(ref="#/definitions/SegmentationInfoResponse")
	 *         ))
	 *
	 * 
	 * @SWG\Definition(definition="SegmentationInfoResponse", type="object", required={"data"},
	 * 		@SWG\Property(property="description", type="string", example="Usuario Abridor",description="Description for bettle knowledge"),
	 * 		@SWG\Property(property="api_name",type="string",example="marketing-opener",description="Name of the api for getting the possible values for that segmentation"),
	 * 		@SWG\Property(property="data_type",type="string",example="simple",enum={"simple","multiple"},description="Data type that the segmentation accepts")
	 * 
	 * 
	 * 
	 * )
	 * 
	 * 
	 * 
	 * 
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showInfo()
	{
		//\DB::listen(function ($sql) {
		//	var_dump($sql->sql, $sql->bindings);
		//});
		
		//dd($a);
		return SegmentationInfoResource::collection(AaaaTableControl::with('data_type')->where('action', '<>', 'ignore')->get());
		//return new SegmentationInfoCollection($a);
		/* $out = [];
		foreach ($a as $k => $val) {
			# code...
			$item = array('description' => $val->description, 'api_name' => $val->api_name, 'data_type' => $val->data_type->name);
			$out[] = $item;
			unset($item);
		}
		$output = ['data' => $out];
		return ($output); */
	}


	/**
	 * 
	 * @SWG\Get(
	 *   path="/segmentations/{api_name}",
	 *   summary="Returns all values of a certain  segmentation available in the system",
	 * tags={"Segmentation Info"},
	 *     description="Api get that returns all values available for performing a segmentation for a specific field",
	 *     produces={ "application/json"},
	 * 		
	 *  *   @SWG\Parameter(
	 *     name="api_name",
	 *     in="path",
	 *     description="api_name field returned at the /segmentation/info api.",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *  * @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 * @SWG\Response(
	 *         response=200,
	 *         description="List of all Segmentations Available",
	 * @SWG\Schema(ref="#/definitions/SegmentationInfoApiResponse")
	 * )
	 * )
	 * 
	 * 
	 * * @SWG\Definition(definition="SegmentationInfoApiResponse", type="object", required={"data"},
	 * @SWG\Property(
	 *             property="data",
	 *             type="array",
	 *             format="int32",
	 * @SWG\Items(
	 *	@SWG\Property(property="id",type="integer",example="1", description="Internal Id"),
	 * @SWG\Property(property="crm_value",type="string",example="35", description="CRM Internal Id"),
	 * @SWG\Property(property="front_value",type="string",example="HOTMAIL.COM", description="Front Value")
	 *)
	 *         )
	 * )
	 *
	 * 
	
	 * 
	 * 
	 * 
	 * @SWG\Definition(definition="SegmentationInfoApiErrorResponses", type="object", required={"error"},
	 * @SWG\Property(
	 *             property="error",
	 *             type="object",
	 *             format="int32",
	 * 		@SWG\Property(property="code", type="string", example="SVS-404",description="Internal Code Error"),
	 * 		@SWG\Property(property="http_code",type="integer",example="404",description="Http Status returned in the header, replicated as information"),
	 * 		@SWG\Property(property="message",type="string",example="Api Resource not Found",description="Additional information about the error ")
	 * 
	 * )
	 * 
	 * )
	 * 
	 * 
	 * Función que devuelve los posibles valores de segmentación de una tabla
	 * Recibe el campo api_name de la tabla de control 
	 * @param string $api_name api_name de la segentación a buscar. 
	 *  @return \Illuminate\Http\Response
	 */
	public function showValuesSegmentation(ApiNameInputValidation $request, $api_name)
	{
		dump($api_name);
		dd($request);
		//Primero chequeamos si se puede utilizar para la segmentación
		$data = AaaaTableControl::where('api_name', $api_name)->get();

		if ($data->count() != 1) {
			return new ErrorResponse(new Error(404, 'Api Resource not found', 'SVS-404'));


		}
		//dd($data);
		$isBit = $data[0]->action == 'bit';
		if ($isBit) {


			return SegmentationInfoValuesResource::collection($data);
		}
		//Si llegamos aquí, tenemos que sacar los valores de la tabla correspondiente.
		$table = $data[0]->name . config('api-crm.table_val_postfix');
		$col = DB::connection('segmentation')->table($table)->get();
		return SegmentationInfoValuesResource::collection($col);



	}



	/**
	 * 
	 * 	  @SWG\Post(
	 *   path="/segmentations",
	 *   summary="Create new Segmentation",
	 * tags={"Segmentation Info"},
	 *     description="Functionality for admin roles. Create new segmentation environment. From now, if the systema has data, can be used as an additional variable for segmentation and get information about users whom match with the segmentation",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 * @SWG\Response(
	 *         response=200,
	 *         description="List of all Segmentations Available",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoResponses")
	 * 
	 
	 *        
	 *     ),
	 *  *  @SWG\Response(
	 *         response=419,
	 *         description="Error Creating table.",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 * 	 * @SWG\Parameter(
	 *     name="body",
	 *     in="body",
	 *     description="Information for creating the segmentation.",
	 *     required=true,
	 *     type="object",
	 * @SWG\Schema(ref="#/definitions/SegmentationPostResponses")
	 *   )
	 * )
	 * 
	 * 

	 * @SWG\Definition(definition="SegmentationPostResponses", type="object", required={"name","action","description","api_name","data_type"},

	 * 		@SWG\Property(property="name", type="string", example="api new segmentation",description="Must be unique in the system. Will be used for internal management and control. If don't know what to send, send the same than the field api_name"),
	 * 		@SWG\Property(property="action",type="string",example="normal",description="Admited values: normal, bit. Normal is for general use. Bit: If the values are 0 or 1, i.e. for openers, or clickers"),
	 * 		@SWG\Property(property="description",type="string",example="Segmentation for geolocation purposes",description="Description of what the segmentation is about"),
	 * @SWG\Property(property="api_name",type="string",example="front segmentation name",description="Name of the segmentation for accesing or segmenting such values"),
	 * @SWG\Property(property="data_type_id",type="integer",example="1",description="Data type that will contain the segmentation: 1->simple, 2->multiple. Simple means for example email domain. An user can only have 1 email domain. Multiple means that an user can have several values, i.e. owner of devices (can have several devices)")
	 
	 * 
	 * 
	 * )
	 * 

	 *
	 * Función api, que crea una nueva segmentación. Debería ser una copia más o menos de la existente en el admin.
	 *
	 * @param Request $request
	 * @return void
	 */
	public function createNewSegmentation(SegmentationSchema $schema, RequestNewFieldTableControl $request)
	{
		$allowCreateTable = config('api-crm.allow_create_table_api');
		if (!$allowCreateTable)
			return new ErrorResponse(new Error(403, 'Operation not allowed', 'SVS-403'));
		//Si llegamos aquí estamos autorizados a crear el campo. REvisamos los datos de entrada
		$schema->setAllowCreateAndRemove(true);
		DB::beginTransaction();
		$reg = new AaaaTableControl();
		$name = str_replace('-', '_', (str_slug(strtolower($request->get('name')))));
		$reg->name = $name;
		$action = strtolower($request->get('action'));
		$reg->action = $action;
		$reg->description = ucwords(trim($request->get('description')));
		$reg->api_name = str_slug(trim($request->get('api_name')));
		$reg->data_type_id = $request->get('data_type_id');
		$reg->save();
		$isBit = $action == 'bit';
		if (!$schema->postCreateTableSystem($name, $isBit) || !$reg->id) {
			DB::rollback();
			return new ErrorResponse(new Error(419, 'Error in creating segmentation table', 'SVS-419'));
		}
		DB::commit();
		return new SegmentationInfoResource($reg);


	}


	/**
	 * 
	 * 
	 * 	  @SWG\Post(
	 *   path="/segmentations/{api_name}",
	 *   summary="Add data for a certain segmentation",
	 * tags={"Segmentation Info"},
	 *     description="Functionality for admin roles. Add values for a specific segmentation. Therefore, such values will be available for segmentation",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="api_name",
	 *     in="path",
	 *     description="api_name field returned at the /segmentation/info api.",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *  @SWG\Parameter(
	 *     name="body",
	 *     in="body",
	 *     description="Information including the values.",
	 *     required=true,
	 *     type="object",
	 * @SWG\Schema(ref="#/definitions/SegmentationPostValuesInput")
	 *   ),
	 * @SWG\Response(
	 *         response=200,
	 *         description="List of all Segmentations Available",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationPostValuesResponses")
	 * ),
	 * 	 *  * @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 * 	  @SWG\Response(
	 *         response=420,
	 *         description="Action cannot be performed on the selected data. Surelly, Table is not availble or is of type 'bit', therefore, there is no relational data for segmentation (segmentation is either 0 or 1)",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )
	 * 
	 * * @SWG\Definition(definition="SegmentationPostValuesInput", type="object", required={"data"},
	 * @SWG\Property(
	 *             property="data",
	 *             type="array",
	 *             format="int32",
	 * 			required={"crm_value","front_value"},
	 * @SWG\Items(
	 * @SWG\Property(property="crm_value",type="string",example="35", description="CRM Internal Id"),
	 * @SWG\Property(property="front_value",type="string",example="HOTMAIL.COM", description="Front Value")
	 *)
	 *         )
	 * )
	 * 
	 * 
	 *  @SWG\Definition(definition="SegmentationPostValuesResponses", type="object", required={"received","errors"},
	 *		@SWG\Property(property="received",type="integer",example="5", description="Number of items received"),
	 * 		@SWG\Property(
	 *             property="errors",
	 *             type="object",
	 * 				required={"count"},
	 * 					@SWG\Property(property="count",type="integer",example="3", description="Number of errors found. These errors are always regarding input data (bad keys, empty values, number of entries not valid) and never if has been inserted or not"),
	 *						@SWG\Property(property="data",type="array",required={"msg"},
	 *							@SWG\Items(	
	 *							@SWG\Property(property="msg",type="string",example="At least 1 Field of the received does not exists in the system", description="Description of the errorInternal Id")
	 *							)
	 *					)
	 *					
										
	 * 		)
	 * 

	 * )
	 * 					

	 * 
	 * Función que recibirá un json con un array de datos, para insertar en  una segmentación específica pasada por url
	 *
	 * @param string $api_name Segmentación sobre la que se crearán los datos
	 * @param Request $request Request recibida formato json
	 * @return array con respuesta
	 */
	public function createValuesSegmentation(ApiNameInputValidation $request, $api_name)
	{
//Primero chequeamos si existe el dato en el sistema
		//Primero chequeamos si se puede utilizar para la segmentación

		$arr_mandatory_fields = ['crm_value' => 'val_crm', 'front_value' => 'val_normalized'];

		$errors = [];
		$insert = [];
		$data = AaaaTableControl::where('api_name', $api_name)->get();

		if ($data->count() != 1) {
			return new ErrorResponse(new Error(404, 'Api Resource not found', 'SVS-404'));
		} elseif ($data[0]->action == 'bit') {
			return new ErrorResponse(new Error(420, 'This Segmentation does not allow additional data', 'SVS-420'));
		}
		$tableName = $data[0]->name . config('api-crm.table_val_postfix');
		//Si llegamos aquí es que existe
		$tbInserted = array();
		$data = $request->get('data');
		$cont = count($data);
		$cont_err = 0;
		//chequeamos que podemos insertar el dato tal cuál...
		foreach ($data as &$datum) {
			$error = false;
			foreach ($arr_mandatory_fields as $field => $fieldInsert) {

				if (!array_key_exists($field, $datum) || empty($datum[$field]) || count(array_keys($datum)) != count($arr_mandatory_fields)) {
					$msgError = '';
					if (!array_key_exists($field, $datum)) {
						$msgError = 'At least 1 Field of the received does not exists in the system';

					} elseif (empty($datum[$field]))
						$msgError = sprintf('Field %s is empty not exists in the system', $field);
					elseif (count(array_keys($datum)) != count($arr_mandatory_fields))
						$msgError = sprintf('# of fields differs from the allowed and expected', $field);

					$datum['reason'] = $msgError;
					$cont_err++;
					$errors[] = $datum;
					$error = true;
					break;
				}
			}
			if ($error)
				continue;
			$tmp = [];
			foreach ($arr_mandatory_fields as $field => $fieldInsert) {
				$tmp[$fieldInsert] = $datum[$field];
			}
			$insert[] = $tmp;
			unset($tmp);



		}
		//Insertamos
		$res = DB::connection('segmentation')->table($tableName)->insertIgnore($insert);

		$output = [];
		$output['errors'] = ['count' => $cont_err, 'data' => $errors];
		$output['received'] = $cont;

		return response()->json($output);
	}
	/**
	 * 
	 * 	  @SWG\Put(
	 *   path="/segmentations/{api_name}/{id}",
	 *   summary="Modify the CRM or Normalized value from a certain segmentation",
	 * tags={"Segmentation Info"},
	 *     description="Functionality for admin roles. Modify values for a specific segmentation. Therefore, such values will be available for segmentation",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="api_name",
	 *     in="path",
	 *     description="api_name field returned at the /segmentation/info api.",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *  @SWG\Parameter(
	 *     name="id",
	 *     in="path",
	 *     description="Id of the value that requires to be updated (from the api_name segmentation)",
	 *     required=true,
	 *     type="integer"
	 *   ),
	 *  @SWG\Parameter(
	 *     name="body",
	 *     in="body",
	 *     description="Information including the values.",
	 *     required=true,
	 *     type="object",
	 * 		@SWG\Property(
	 * 			property="data",
	 * 			type="array",
	 * 			@SWG\Items(
	 * 				required={"input_data_mode","value"},
	 * 				@SWG\Property(
	 * 					property="input_data_mode",
	 * 					description="Type of value to be updated. It can be the CRM value (crm) or the normalized value (val)",
	 * 					type="string",
	 * 					enum={"crm","val"},
	 * 					example="crm"
	 * 				),
	 * 				@SWG\Property(
	 * 					property="value",
	 * 					description="New value to be updated",
	 * 					type="string",
	 * 					example="HOTMAIL.COM"
	 * 				)
	 * 			)
	 * 		)
	 * 	)
	 * 
	 * 
	 * 
	 * ,
	 * 		@SWG\Response(
	 *      	response=200,
	 *         	description="List of all Segmentations Available",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * 		),
	 * 	 *  * @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")       
	 *     	),
	 * 	  @SWG\Response(
	 *         response=420,
	 *         description="Action cannot be performed on the selected data. Surelly, Table is not availble or is of type 'bit', therefore, there is no relational data for segmentation (segmentation is either 0 or 1)",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 *		)
	 * )
	 * 
	 * 
	 * No desarrollada. Modificará valores de segmentación
	 *
	 * @param string $api_name segmentación a actualizar
	 * @param Request $request Datos provenientes
	 * @return void
	 */
	public function updateValuesSegmentation(ApiNameInputValidation $request, $api_name, $id)
	{
		//Prmero vemos de qué tipo es
		$data = AaaaTableControl::where('api_name', $api_name)->get();

		if ($data->count() != 1) {
			return new ErrorResponse(new Error(404, 'Api Resource not found', 'SVS-404'));
		} elseif ($data[0]->action == 'bit' || $data[0]->action == 'ignore') {
			return new ErrorResponse(new Error(403, 'Operation not allowed for this segmentation', 'SVS-403'));
		}
		$tableName = $data[0]->name;
		$tableNameVals = $tableName . config('api-crm.table_val_postfix');
		//Vemos a ver si existe el valor, y si existe,si hay elementos con dichos datos
		$dat = DB::connection('segmentation')->table($tableNameVals)->find($id);
		if (empty($dat)) {
			return new ErrorResponse(new Error(404, 'Id value to be removed not found', 'SVS-404'));

		}
		$data = $request->get('data');
		if (empty($data) || count($data) > 2) {
			return new ErrorResponse(new Error(403, 'Empty request or too many elements to update', 'SVS-403'));

		}
		$arrUpd = [];
		//Algunas validaciones las puede hacer laravel antes de recibir los datos. De momento las ponemos aquí
		foreach ($data as $datum) {
			$inputDataMode = trim($datum['input_data_mode']);
			if (!($inputDataMode == 'crm' || $inputDataMode == 'val')) {
				return new ErrorResponse(new Error(403, 'Wrong input_data_mode: ' . $inputDataMode . ' not allowed', 'SVS-403'));

			}
			$val = trim($datum['value']);
			if (!is_string($val))
				return new ErrorResponse(new Error(403, 'Wrong value: ' . $val . ' not allowed', 'SVS-403'));

			$field = ($inputDataMode == 'crm') ? 'val_crm' : 'val_normalized';
			$arrUpd[$field] = $val;
		}
		if (!empty($arrUpd)) {

			DB::connection('segmentation')->table($tableNameVals)->where('id', $id)->update($arrUpd);
		}


		return response()->json(array('data' => array('id' => $id, 'result' => 1, 'msg' => count($arrUpd) . ' have been updated')));


	}
	/**
	 * 
	 * 	  @SWG\Delete(
	 *   path="/segmentations/{api_name}/{id}",
	 *   summary="Delete a value from a certain segmentation",
	 * tags={"Segmentation Info"},
	 *     description="Functionality for admin roles. Remove a value from a specific segmentation ONLY if there is not user assigned to such value. If value is removed, won`t be available for segmentation",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="api_name",
	 *     in="path",
	 *     description="api_name field returned at the /segmentation/info api.",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *  @SWG\Parameter(
	 *     name="id",
	 *     in="path",
	 *     description="Id of the value that requires to be removed (from the api_name segmentation)",
	 *     required=true,
	 *     type="integer"
	 *   ),
	 * @SWG\Response(
	 *         response=200,
	 *         description="List of all Segmentations Available",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * ),
	 * 	 *  * @SWG\Response(
	 *         response=404,
	 *         description="id or api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 * 	  @SWG\Response(
	 *         response=403,
	 *         description="Action cannot be performed on the selected data. Surelly cannot be removed due to data assigned to that value",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * ) 
	 * 
	 * 
	 * 
	 * Eliminará datos de segmentación, SOLO si no existen datos asociados a dichos datos
	 *
	 * @param string $api_name segmentación a actualizar
	 * @param Request $request Datos provenientes
	 * @return void
	 */
	public function deleteValuesSegmentation(ApiNameInputValidation $request, $api_name, $id)
	{
		//Prmero vemos de qué tipo es
		$data = AaaaTableControl::where('api_name', $api_name)->get();

		if ($data->count() != 1) {
			return new ErrorResponse(new Error(404, 'Api Resource not found', 'SVS-404'));
		} elseif ($data[0]->action == 'bit' || $data[0]->action == 'ignore') {
			return new ErrorResponse(new Error(403, 'Operation not allowed for this segmentation', 'SVS-403'));
		}
		$tableName = $data[0]->name;
		$tableNameVals = $tableName . config('api-crm.table_val_postfix');
		//Vemos a ver si existe el valor, y si existe,si hay elementos con dichos datos
		$dat = DB::connection('segmentation')->table($tableNameVals)->find($id);
		if (empty($dat)) {
			return new ErrorResponse(new Error(404, 'Id value to be removed not found', 'SVS-404'));

		}
		$cont = DB::connection('segmentation')->table($tableName)->where('id_val', $id)->count();
		if ($cont > 0) {
			return new ErrorResponse(new Error(403, 'Operation not allowed. Exists users´ data for this segmentation. Please contact Netsales for further information (' . $cont . ')', 'SVS-403'));

		}
		DB::connection('segmentation')->table($tableNameVals)->where('id', $id)->delete();
		//Lo borramos de redis si existe
		$redisPrefix = config('api-crm.redis_crm_prefix');
		Redis::del($redisPrefix.$tableName.':'.$id);
		return response()->json(array('data' => array('id' => $id, 'result' => 1, 'msg' => 'Data ' . $id . ' Removed from the api ' . $api_name)));


	}







	/*****************************************CRM Data Load *************************************** */
	/********************************************************************************************** */


	/**
	 * 
	 * 	  @SWG\Post(
	 *   path="/data/loads",
	 *   summary="Synchro CRM Data with Api-Crm",
	 * tags={"Synchro Data","Referential-Segmentation Data"},
	 *     description="Functionality for admin roles. Update/Insert values for users. As entry api will receive idchannels and what will be updated for those idchannels",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 * 
	 *  @SWG\Parameter(
	 *     name="body",
	 *     in="body",
	 *     description="Data to be included or updated including the values.",
	 *     required=true,
	 *     type="object",
	 * @SWG\Schema(ref="#/definitions/createUsersByIdCRMDataInput")
	 *   ),
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response of the Data Load. Notice it is included in a Queue and will be processed in background",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * 	),
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )
	 * 
	 * @SWG\Definition(definition="createUsersByIdCRMDataInput", type="object", required={"data"},
	 *		@SWG\Property(
	 *			property="data",
	 *			type="array",
	 *			description="Array with the input data",
	 *			required={"id","api_name","input_data_mode"},
	 *				@SWG\Items(
	 *					@SWG\Property(
	 *						property="id",
	 *						type="array",
	 *						description="Array de idchannels a actualizar/insertar. Al menos debe venir 1 elemento",	
	 *						@SWG\Items(
	 *							type="integer",
	 *							format="int64",
	 *							example={34,45,87,123}	
	 *						)
	 *					),
	 *					@SWG\Property(
	 *						property="api_name",
	 *     					description="api_name field returned at the /segmentation/info api.",
	 *     					example="api-name",
	 *     					type="string"
	 *					),
	 *					@SWG\Property(
	 * 						property="input_data_mode",
	 * 						type="string",
	 * 						description="Type of input data. It can be the ID of the value (id), the CRM value (crm) or the normalized value (val)",
	 * 						enum={"id","crm","val"},
	 * 						example="crm"
	 * 					),
	 * 					@SWG\Property(
	 * 						property="val",
	 * 						type="array",
	 * 						description="Array of values or ids to update from the api_name segmentation. Values data type can vary depending on the input_data_mode. If it is 'id' will be integer, i.o.c. will be string. If the api_name is the type 'bit' only will accept either 0 or 1, and the first element of the array if the number of elements are more than 1. The same will happen if the field does not allow multiple data, 'simple' where only the first element of the array will be processed",
	 * 						@SWG\Items(
	 * 							type="variant",
	 * 							example={"HOTMAIL.COM","GMAIL.COM"}
	 * 						)
	 * 					)
	 *				)		
	 *		)
	 *
	 *  )
	 * 
	 * Funcionalidad que recoge los datos de entrada, e inserta los datos en la tabla de data_loads para su posterior processamiento
	 * La entrada de datos es por Id del usuario, y puede ser multivalor (multitable)
	 *
	 * @param Request $request DAtos de entrada
	 * @return void
	 */
	public function createUsersByIdCRMData(CreateUsersByIdApiRequest $request)
	{
		$realRequest = $request->all();
		if (empty($realRequest) || count($realRequest) == 0)
			return new ErrorResponse(new Error(421, 'Empty Request', 'SVS-421'));
		$requestJson = json_encode($realRequest);
		$uuid = Uuid::generate(4)->string;
		$dataLoad = new DataLoad();
		$dataLoad->functionality = 'createUsersByIdCRMData';
		$dataLoad->request = $requestJson;
		$dataLoad->uuid_token = $uuid;
		$dataLoad->save();
		$id = $dataLoad->id;
		if (!$id)
			return new ErrorResponse(new Error(423, 'Error Creating Job', 'SVS-423'));
		ProcessDataLoadJob::dispatch($dataLoad);
		return response()->json(array('data' => array('id' => $uuid, 'result' => 1, 'msg' => 'Data Received. Will be processed in background')));


	}



	/**
	 * 
	 * @SWG\Post(
	 *   path="/data/loads/{api_name}",
	 *   summary="Synchro CRM Data with Api-Crm",
	 * tags={"Synchro Data","Referential-Segmentation Data"},
	 *     description="Functionality for admin roles. Update/Insert values for a certain segmentation (segmentation as path in the URL). The Data will be processed in background, so you will need to check the specific api for checking the status of the process",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="api_name",
	 *     in="path",
	 *     description="api_name field returned at the /segmentation/info api.",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *  @SWG\Parameter(
	 *     name="body",
	 *     in="body",
	 *     description="Data to be included or updated including the values.",
	 *     required=true,
	 *     type="object",
	 * @SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataInput")
	 *   ),
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response of the Data Load. Notice it is included in a Queue and will be processed in background",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * 	),
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )
	 * @SWG\Definition(definition="CreateUsersByApiNameCRMDataInput", type="object", required={"input_data_mode","data"},
	 *		@SWG\Property(
	 * 			property="input_data_mode",
	 * 			type="string",
	 * 			description="Type of input data. It can be the ID of the value (id), the CRM value (crm) or the normalized value (val)",
	 * 			enum={"id","crm","val"},
	 * 			example="crm"
	 * 		),
	 * 		@SWG\Property(
	 * 			property="data",
	 * 			type="array",
	 * 			description="Array with information about data to be inserted or updated",
	 * 			required={"id","val"},
	 * 			@SWG\Items(
	 * 				@SWG\Property(property="id",type="variant",description="If input_data_mode is id, this data will be numeric, as it will be the ID of the value, i.o.c it will be String. If the table is of type bit, it will only be either 0 or 1",example="HOTMAIL.COM"),
	 * 				@SWG\Property(property="val",type="array",description="Array with the Channelsid to be updated or inserted",example={12,13,15},
	 * 					@SWG\Items(
	 * 						type="integer",
	 * 						format="int64"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 *  )
	 * @SWG\Definition(definition="CreateUsersByApiNameCRMDataResponse", type="object", required={"data"},
	 *		@SWG\Property(
	 *             property="data",
	 *             type="object",
	 * 				required={"id","result","msg"},
	 * 			@SWG\Property(
	 * 				property="id",
	 * 				type="uuid",
	 * 				description="Id of the request created (uuid format)",
	 * 				example="ce68c0ca-075b-11e8-ba89-0ed5f89f718b"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="result",
	 * 				type="integer",
	 * 				description="Result of inserting the request in the queue. 1 Correct, 0 Incorrect",
	 * 				example=1
	 * 			),
	 * 			@SWG\Property(
	 * 				property="msg",
	 * 				type="string",
	 * 				description="Additional string of the response generated",
	 * 				example="Data Received. Will be processed in background"
	 * 			)
	 *      )
	 * )
	 * Funcionalidad que recoge los datos de entrada e inserta los datos en la tabla data_loads para su posterior procesamiento
	 * La entrada de datos viene dado por la segmentación y un listado de idchannels asociados 
	 *
	 * @param string $api_name segmentación a actualizar
	 * @param Request $request
	 * @return void
	 */
	public function createUsersByApiNameCRMData(CreateUsersByNameApiRequest $request, $api_name)
	{
		//Recogemos la query realmente como viene
		$realRequest = $request->all();
		if (empty($realRequest) || count($realRequest) == 0)
			return new ErrorResponse(new Error(421, 'Empty Request', 'SVS-421'));

		$data = AaaaTableControl::where('api_name', $api_name)->get();

		if ($data->count() != 1) {
			return new ErrorResponse(new Error(404, 'Api Resource not found', 'SVS-404'));
		}
		$uuid = Uuid::generate(4)->string;
		$requestJson = json_encode($realRequest);
		$dataLoad = new DataLoad();
		$dataLoad->functionality = 'createUsersByApiNameCRMData';
		$dataLoad->request = $requestJson;
		$dataLoad->api_name = $api_name;
		$dataLoad->uuid_token = $uuid;
		$dataLoad->save();
		$id = $dataLoad->id;

		ProcessDataLoadJob::dispatch($dataLoad);




		if (!$id) {
			return new ErrorResponse(new Error(423, 'Error Creating Job', 'SVS-423'));
		}
		return response()->json(array('data' => array('id' => $uuid, 'result' => 1, 'msg' => 'Data Received. Will be processed in background')));


	}





	/**
	 * 
	 * @SWG\get(
	 *   path="/data/loads/info/{uuid_token}",
	 *   summary="Retrieve information about the status of the queued Bulk Data Load",
	 * 	tags={"Synchro Data","Referential-Segmentation Data"},
	 *   description="Functionality that returns for admin roles. Update/Insert values for a certain segmentation (segmentation as path in the URL). The Data will  processed in background, so you will need to check the specific api for checking the status of the process",
	 *   produces={ "application/json"},
	 *  @SWG\Parameter(
	 *     name="uuid_token",
	 *     in="path",
	 *     description="uuid_token generated for the load in background. Notice: uuid format https://es.wikipedia.org/wiki/Identificador_%C3%BAnico_universal",
	 *     required=true,
	 *     type="string"
	 *   )
	 * ,
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response of the Data Load. Notice it is included in a Queue and will be processed in background",
	 * 			@SWG\Schema(ref="#/definitions/LoadDataInfoResponse")
	 * 	),
	 * 
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="uuid_token not correct or not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 *) 
	 * 
	 * @SWG\Definition(definition="LoadDataInfoResponse", type="object", required={"data"},
	 * 		@SWG\Property(
	 * 			property="data",
	 * 			type="object",
	 * 			required={"uuid_token","functionality","api_name","request"},
	 * 			@SWG\Property(
	 * 				property="uuid_token",
	 * 				type="string",
	 * 				description="uuid token of the item",
	 * 				example="d0e7facd-6778-41b4-8e30-5ead387f7478"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="functionality",
	 * 				type="string",
	 * 				description="Functionality required originally",
	 * 				example="createUsersByIdCRMData"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="api_name",
	 * 				type="string",
	 * 				description="api_name field required in the request (if any)",
	 * 				example="api-name"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="request",
	 * 				type="string",
	 * 				description="Original request in Json structure",
	 * 				example="{'data':[{'id':[15],'api_name':'api-name','input_data_mode':'id','val':[1,2,4]},{'id':[15,13,14],'api_name':'marketing-opener','input_data_mode':'id','val':[1,0,4]}]}"
	 * 				
	 * 			),
	 * 			@SWG\Property(
	 * 				property="response",
	 * 				type="string",
	 * 				description="If not empty, the status of the process (OK or fail). If empty or null means that has not been processed yet.",
	 * 				example="OK"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="response_errors",
	 * 				type="string",
	 * 				description="If not empty, all errors found in Json structure.",
	 * 				example=""
	 * 			),
	 * 			@SWG\Property(
	 * 				property="cont_input",
	 * 				type="integer",
	 * 				description="Number of unique idchannels that have been updated.",
	 * 				example=25
	 * 			),
	 * 			@SWG\Property(
	 * 				property="cont_processed",
	 * 				type="integer",
	 * 				description="Total number of inserts/updates performanced.",
	 * 				example=32
	 * 			),
	 * 			@SWG\Property(
	 * 				property="processed",
	 * 				type="integer",
	 * 				description="0 if has not been processed. 1 have been processed. 2 being processed at this moment.",
	 * 				example=1
	 * 			),
	 * 			@SWG\Property(
	 * 				property="processed_at",
	 * 				type="date",
	 * 				description="if not null, date was processed.",
	 * 				example="2018-03-02 21:02:09"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="created_at",
	 * 				type="date",
	 * 				description="Date the request was made.",
	 * 				example="2017-03-02 21:02:09"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="modified_at",
	 * 				type="date",
	 * 				description="Date the request was modified.",
	 * 				example="2017-03-02 22:02:09"
	 * 			)
	 * 		)
	 * )
	 * 
	 * Funcionalidad que dado un uuid de entrada, devuelve el estado de una request de carga
	 *
	 * @param ApiLoadInfoUuidValidation $request
	 * @param uuid $uuid_token
	 * @return void
	 */
	public function infoLoadData(ApiLoadInfoUuidValidation $request, $uuid_token)
	{
		$data = DataLoad::where('uuid_token', $uuid_token)->get();
		return response()->json(['data' => $data], 200);

	}

	/**
	 * @SWG\get(
	 *   path="/data/data-bases/list",
	 *   summary="Retrieve information about the active Data Bases",
	 * 	tags={"Synchro Data","Data Base"},
	 *   description="Functionality that returns all Active Data Bases that can be used for retrieving information",
	 *   produces={ "application/json"},

	 * @SWG\Response(
	 *         response=200,
	 *         description="List of all available and active DBswill be processed in background",
	 * 			@SWG\Schema(ref="#/definitions/getDataBaseListResponse")
	 * 	)
	 *) 


	 * @SWG\get(
	 *   path="/data/data-bases/list/all",
	 *   summary="Retrieve information about all Data Bases",
	 * 	tags={"Synchro Data","Data Base"},
	 *   description="Functionality that returns all Data Bases, either active or non-active",
	 *   produces={ "application/json"},

	 * @SWG\Response(
	 *         response=200,
	 *         description="List of all available and active DBswill be processed in background",
	 * 			@SWG\Schema(ref="#/definitions/getDataBaseListResponse")
	 * 	)
	 *) 

	 * @SWG\Definition(definition="getDataBaseListResponse", type="object", required={"data"},
	 * 		@SWG\Property(
	 * 			property="data",
	 * 			type="array",
	 * 			description="Inside this tag will be included the DB list",
	 * 			@SWG\Items(
	 * 				ref="#/definitions/getDataBaseListItemResponse"
	 * 				
	 * 			)
	 * 		)
	 * )
	 * 
	 * @SWG\Definition(definition="getDataBaseListItemResponse", type="object", required={"id"},
	 * 		@SWG\Property(
	 * 			property="id",
	 * 			type="integer",
	 * 			description="Identifier of the DB (CRMid).",
	 * 			example=9
	 * 		),
	 * 		@SWG\Property(
	 * 			property="val",
	 * 			type="string",
	 * 			description="Name of the BBDD (slug in upper case).",
	 * 			example="DJK"
	 * 		),
	 * 		@SWG\Property(
	 * 			property="active",
	 * 			type="integer",
	 * 			description="1 if the DB is active. 0 if it is unactive.",
	 * 			example=1
	 * 		),
	 * 		@SWG\Property(
	 * 			property="created_at",
	 * 			type="date",
	 * 			description="Date BBDD was created in the Api",
	 * 			example="2018-02-08 12:42:00",
	 * 		),
	 * 		@SWG\Property(
	 * 			property="updated_at",
	 * 			type="date",
	 * 			description="Date BBDD was updated in the Api",
	 * 			example=null,
	 * 		)
	 * )
	 *
	 * Función get que devolverá el listado de BBDD que están en el sistema y que podrán ser gestionadas
	 *
	 * @return json with the required data
	 */
	public function getDataBaseList($status = null)
	{

		$query = DB::connection('segmentation')->table('bbdd_lists');

		if (is_null($status)) {
			$query->where('active', 1);
		}
		$data = $query->get();

		return response()->json(['data' => $data], 200);
	}

	/**
	 * 
	 * @SWG\Post(
	 *   path="/data/data-bases",
	 *   summary="Insert new DB",
	 * tags={"Synchro Data","Data Base"},
	 *     description="Functionality for admin roles. Insert new DB in the system",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},

	 *  @SWG\Parameter(
	 *     name="body",
	 *     in="body",
	 *     description="Data of the new DB.",
	 *     required=true,
	 *     type="object",
	 * 		@SWG\Property(
	 * 			required={"id","bbdd","active"},
	 * 			@SWG\Property(
	 * 				property="id",
	 * 				type="integer",
	 * 				format="int64",
	 * 				description="DB Id from the CRM",
	 * 				example=9
	 * 			),
	 * 			@SWG\Property(
	 * 				property="bbdd",
	 * 				type="string",
	 * 				description="Name of the DB. Must be unique, with a max length of 5 chars. Will be stored in Upper Case",
	 * 				example="DJK"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="active",
	 * 				type="integer",
	 * 				description="If the DB will be inserted as active or non active (1 active, 0 unactive)",
	 * 				example=0
	 * 			)
	 * 		)
	 *   ),
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response Correct",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * 	),
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )
	 * 
	 * 
	 * 
	 * 
	 * Función que inserta el dato en la bbdd
	 *
	 * @return void
	 */
	public function postDataBaseList(DataBasePostInputRequest $request)
	{
		$ins = ['id' => $request->id, 'val' => strtoupper($request->bbdd), 'active' => $request->active];
		DB::connection('segmentation')->table('bbdd_lists')->insert([
			$ins
		]);
		return response()->json(array('data' => array('id' => $request->id, 'result' => 1, 'msg' => 'Data Received and inserted correctly')));

	}

	/**

	 * 	  @SWG\Put(
	 *   path="/data/data-bases/{idbbdd}",
	 *   summary="Modify the Name of the DB",
	 * tags={"Synchro Data","Data Base"},
	 *     description="Functionality that update the Name of the DB. Notice that the new value must be unique, between 2 and 5 chars and will be stored in Upper Case. Active input value will be updated only for Super Administrator Roles.",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="idbbdd",
	 *     in="path",
	 *     description="Id of the DB in the system that will be updated.",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *  @SWG\Parameter(
	 *     name="body",
	 *     in="body",
	 *     description="Data of updated DB.",
	 *     required=true,
	 *     type="object",
	 * 		@SWG\Property(
	 * 			required={"bbdd","active"},
	 * 			
	 * 			@SWG\Property(
	 * 				property="bbdd",
	 * 				type="string",
	 * 				description="Name of the DB. Must be unique, with a max length of 5 chars. Will be stored in Upper Case",
	 * 				example="DJK"
	 * 			),
	 * 			@SWG\Property(
	 * 				property="active",
	 * 				type="integer",
	 * 				description="Only will be updated for Administrator Roles. if the DB will be updated to active or unactive (1 active, 0 unactive)",
	 * 				example=0
	 * 			)
	 * 		)
	 *   ),
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response Correct",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * 	),
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )

	 * Modifica las bbdds existentes
	 * 
	 * @param DataBasePostInputRequest $request la request ya validada
	 * @param integer $idbbdd identificador del elemento a actualizar
	 *
	 * @return void
	 */
	public function putDataBaseList(DataBasePostInputRequest $request, $idbbdd)
	{
//Lo primero cogemos el elemento

		$bbddItem = DB::connection('segmentation')->table('bbdd_lists')->find($idbbdd);
		if (empty($bbddItem)) {
			return new ErrorResponse(new Error(404, 'Resource Not Found', 'SVS-404'));

		}
		//De momento solo desactivamos y modificamos el campo val
		//Hay que tener en cuenta que si la bbdd es modificada, de activación a desactivación hay que hacer muchas más cosas... quizás solo un superadmin podrá hacerlo
		//Pero cuidado, 

		if (isset($request->active) && Auth::user()->hasRole('SuperAdmin')) {
			ProcessActivateDeactivateJob::dispatch(['action' => (($request->active == 1) ? 'activate' : 'deactivate'), 'idBbdd' => $idbbdd]);

		}

		DB::connection('segmentation')->table('bbdd_lists')->where('id', $idbbdd)->update(['val' => strtoupper(trim($request->bbdd)), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
		return response()->json(array('data' => array('id' => $idbbdd, 'result' => 1, 'msg' => 'Data Received and updated correctly (active not updated)')));

	}


	/**
	 * 
	 * 	  @SWG\Put(
	 *   path="/data/data-bases/{idbbdd}/{action}",
	 *   summary="Activate or deactivate a certain DB",
	 * tags={"Synchro Data","Data Base"},
	 *     description="Functionality for Administrator Role. Will activate or deactivate depending on the action parameter.",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="idbbdd",
	 *     in="path",
	 *     description="Id of the DB in the system that will be updated.",
	 *     required=true,
	 *     type="string"
	 *   ),
	 *  @SWG\Parameter(
	 *     name="action",
	 *     in="path",
	 *     description="Id of the DB in the system that will be updated. Allowed Values: activate, deactivate",
	 *     required=true,
	 *     type="string",
	 * 		enum={"activate","deactivate"}
	 *   ),
	 *  
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response Correct",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * 	),
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )
	 * 
	 * 
	 * 
	 * 
	 * 
	 * Función que activa o desactiva 
	 * IMPORTANTE: esta función solo es para admin
	 * Lleva bastantes connotaciones en background que habría que ver
	 *
	 * @param integer $idbbdd identificador de la bbdd
	 * @param string $action enum(activate,deactivate)
	 * @return void
	 */
	public function putDataBaseListActivateDeactivate(DataBasePutActivateDeactivateInputRequest $request, $idbbdd, $action)
	{
		//REdundante. Ya hemos hecho la comprobaciónen la validación
/* 		$bbddItem = DB::connection('segmentation')->table('bbdd_lists')->find($idbbdd);
		if (empty($bbddItem)) {
			return new ErrorResponse(new Error(404, 'Resource Not Found', 'SVS-404'));

		} */
//Redundante. Esta validaciónya la hace en el objeto de validación de la request
		switch ($action) {
			case 'activate':
				# code...
				$res = 1;
				break;
			case 'deactivate':
				$res = 0;
				break;
			default:
				/**
				 * Esto no hará falta ya que los datos de entrada vienen validados
				 */
				return new ErrorResponse(new Error(403, 'Action not allowed or not found', 'SVS-403'));

				break;
		}
		ProcessActivateDeactivateJob::dispatch(['action' => $action, 'idBbdd' => $idbbdd]);
		/*DB::connection('segmentation')->table('bbdd_lists')->where('id', $idbbdd)->update(['active' => $res, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
		 */
		return response()->json(array('data' => array('id' => $idbbdd, 'result' => 1, 'msg' => 'Data Received and updated correctly ')));
	}




/**


	 * 
	 * 	  @SWG\Put(
	 *   path="/data/users/{idbbdd}/unsub",
	 *   summary="Unsub users from a certain DB",
	 * tags={"Synchro Data","Users"},
	 *     description="Functionality for Administrator Role. Given a certain DB (query parameter), and a list of idChannels, the api will unsub users from that DB. If the user is unique for that DB, will remove data from the segmentations.",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="idbbdd",
	 *     in="path",
	 *     description="Id of the DB in the system that will be updated.",
	 *     required=true,
	 *     type="string"
	 *   ),

	 *  
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response Correct",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * 	),
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )

	 	 * 
	 * 	  @SWG\Post(
	 *   path="/data/users/{idbbdd}/register",
	 *   summary="Register users from a certain DB",
	 * tags={"Synchro Data","Users"},
	 *     description="Functionality for Administrator Role. Given a certain DB (query parameter), and a list of idChannels, the api will Register such users in that DB",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="idbbdd",
	 *     in="path",
	 *     description="Id of the DB in the system that will be updated.",
	 *     required=true,
	 *     type="string"
	 *   ),

	 *  
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response Correct",
	 * 			@SWG\Schema(ref="#/definitions/CreateUsersByApiNameCRMDataResponse")
	 * 	),
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )



* Función que da de alta o baja usuarios en una bbdd
 * Lo mete en un job
 *
 * @param UsersByDBInputRequest $request
 * @param integer $idbbdd Identificador de bbdd
 * @param string $action acción a realizar: activate / deactivate
 * @return void
 */
public function putActivateDeactivateUsersInDB(UsersByDBInputRequest $request, $idbbdd, $action) {
	
	ProcessActivateDeactivateUsersFromDBJob::dispatch(['type'=>'byBBDD','action' => $action, 'idBbdd' => $idbbdd,'requested'=>$request->all()]);
	/*DB::connection('segmentation')->table('bbdd_lists')->where('id', $idbbdd)->update(['active' => $res, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
	 */
	return response()->json(array('data' => array('id' => $idbbdd, 'result' => 1, 'msg' => 'Data Received and updated correctly. Will be updated asap')));
}



	/**
	 * 
	 * @SWG\Post(
	 *   path="/segmentation/v1/counts",
	 *   summary="Api for counting a specific segmentation",
	 * tags={"Segmentation Operations"},
	 *     description="Api that receives a certain information. Api returns number of users that matchs exactly with the desired segmentation",
	 *     produces={ "application/json"},
	 * consumes={"application/json"},
	 *  @SWG\Parameter(
	 *     name="body",
	 *     in="body",
	 *     description="Json that includes information for segmentate.",
	 *     required=true,
	 *     type="object",
	 * @SWG\Schema(ref="#/definitions/CounterDataInputSchema")
	 *   ),
	 * @SWG\Response(
	 *         response=200,
	 *         description="Response of the Segmentation",
	 * 			@SWG\Schema(ref="#/definitions/CounterDataInputSchemaResponse")
	 * 	),
	 *	 @SWG\Response(
	 *         response=404,
	 *         description="api_name not found",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     ),
	 *  @SWG\Response(
	 *         response=422,
	 *         description="Error of input data validation. Can be either lack of data or lack of required fields, or duplicated api_name or name fields",
	 * 			@SWG\Schema(ref="#/definitions/SegmentationInfoApiErrorResponses")
	 * 
	 
	 *        
	 *     )
	 * )
	 * 
	 * 
	 * @SWG\Definition(definition="CounterDataInputSchema", type="object", required={"segmentation","input_data_mode","bbdd_strict_order","limits","bbdd"},
	 * 
	 * 		@SWG\Property(
	 * 			property="input_data_mode",
	 * 			type="string",
	 * 			description="Type of input data. It can be the ID of the value (id), the CRM value (crm) or the normalized value (val). It is VERY IMPORTANT due to all queries of searching data will depends on this value",
	 * 			enum={"id","crm","val"},
	 * 			example="crm"
	 * 		),
	 * 		@SWG\Property(
	 * 			property="bbdd_strict_order",
	 * 			type="integer",
	 * 			description="If is set to 1, output counter will depends on the order the Data Bases Lists is received",
	 * 			enum={0,1},
	 * 			example=1
	 * 		),
	 * 		@SWG\Property(
	 * 			property="kid",
	 * 			type="integer",
	 * 			description="Kami identifier. Not Required. If is set to 1, output counter will depends on the order the Data Bases Lists is received",
	 * 			example=345
	 * 		),
	 * 		@SWG\Property(
	 * 			property="token",
	 * 			type="uuid",
	 * 			description="Token generated in a first Counter Action. Not implemented.",
	 * 			example="d0e7facd-6778-41b4-8e30-5ead387f7478"
	 * 		),
	 * 		@SWG\Property(
	 * 			property="segmentation",
	 * 			type="object",
	 * 			description="Input data object with the required segmentation",
	 * 			required={"data"},
	 * 			@SWG\Property(
	 * 				property="data",
	 * 				type="array",
	 * 				description="List of different segmentations required",
	 * 				@SWG\Items(
	 * 					
	 * 						@SWG\Property(
	 * 							property="api_name",
	 * 							type="string",
	 * 							description="api_name field returned at the /segmentation/info api",
	 * 							example="api-name"
	 * 						),
	 * 						@SWG\Property(
	 * 							property="value",
	 * 							type="array",
	 * 							description="If input_data_mode is id, this data will be numeric, as it will be the ID of the value, i.o.c it will be String. If the table is of type bit, it will only be either 0 or 1 and only will be loaded the first element of the array",
	 * 							example={3,4,56},
	 * 							@SWG\Items(
	 * 						type="integer",
	 * 						format="int64"
	 * 					)
	 * 						)
	 * 					
	 * 				)
	 * 			)
	 * 		),
	 * 		@SWG\Property(
	 * 			property="limits",
	 * 			type="object",
	 * 			description="Limits for returning data. More information will be added soon",
	 * 			required={"limit"},
	 * 			@SWG\Property(
	 * 				property="limit",
	 * 				type="integer",
	 * 				format="int64",
	 * 				description="Total of number of users returned",
	 * 				example=234000
	 * 
	 * 			)
	 * 
	 * 		),
	 * 		@SWG\Property(
	 * 			property="bbdd",
	 * 			type="array",
	 * 			description="List of Data Bases required for getting the data",
	 * 			example={"NET","DJK"},
	 * 			@SWG\Items(
	 * 				type="string"
	 * 
	 * 			)
	 * 		)
	 * 
	 * )
	 * 
	 * @SWG\Definition(definition="CounterDataInputSchemaResponse", type="object", required={"totitems","totsegmentation","data","errors"},
	 * 		@SWG\Property(
	 * 			property="totitems",
	 * 			type="integer",
	 * 			description="Number of users found, and which limit will be the limit number from the request limits->limit",
	 * 			example=10000
	 * 		),
	 * 		@SWG\Property(
	 * 			property="totsegmentation",
	 * 			type="integer",
	 * 			description="Number of users found, and NOT HAVING in mind the limit number from the request limits->limit",
	 * 			example=23909
	 * 		),
	 * 		@SWG\Property(
	 * 			property="data",
	 * 			type="array",
	 * 			description="Array with information, by Data Base (from the bbdd array of the request)",
	 * 			required={"idbbdd","bbdd","tot","unique","urlFile"},
	 * 			@SWG\Items(
	 * 				@SWG\Property(
	 * 					property="idbbdd",
	 * 					description="Data Base Id",
	 * 					type="integer",
	 * 					format="int64",
	 * 					example=9
	 * 				),
	 * 				@SWG\Property(
	 * 					property="bbdd",
	 * 					description="Slug of the Data Base",
	 * 					type="string",
	 * 					example="DJK"
	 * 				),
	 * 				@SWG\Property(
	 * 					property="tot",
	 * 					description="Total number of users found for the segmentation.",
	 * 					type="integer",
	 * 					format="int64",
	 * 					example=147065
	 * 				),
	 * 				@SWG\Property(
	 * 					property="unique",
	 * 					description="Unique number of users found for the segmentation, with the order of the Data Base from the Request.",
	 * 					type="integer",
	 * 					format="int64",
	 * 					example=2069
	 * 				),
	 * 				@SWG\Property(
	 * 					property="urlFile",
	 * 					description="Url of the file for that BBDD that can be downloaded. The file is a CSV file with only 1 column of users Id (Id Channels)",
	 * 					type="string",
	 * 					example="http:\/\/lrv-crm-api.test\/storage\/segmentationFiles\/e\/1b\/e1bc37bf-a008-48a0-9f37-08ec6da2a484-DJK.csv"
	 * 				)
	 * 			)
	 * 		),
	 * 		@SWG\Property(
	 * 			property="errors",
	 * 			type="object",
	 * 			description="Errors found during segmentation",
	 * 			required={"numErrors","data"},
	 * 			@SWG\Property(
	 * 				property="numErrors",
	 * 				type="integer",
	 * 				description="Number of errors found during segmentation",
	 * 				example=0
	 * 			),
	 * 			@SWG\Property(
	 * 				property="data",
	 * 				type="array",
	 * 				description="List of errors description",
	 * 				@SWG\Items(
	 * 					@SWG\Property(
	 * 						property="msg",
	 * 						type="string",
	 * 						description="Error Description",
	 * 						example="Ignored Column [Name of the Column] (Value not admited)"
	 * 
	 * 					),
	 * 					@SWG\Property(
	 * 						property="errorCode",
	 * 						type="integer",
	 * 						description="Error Code",
	 * 						example=421
	 * 
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 * 
	 * La madre de todas las apis.
	 * Realizará un conteo dada una segmentación de entrada
	 * El sistema creará tantos ficheros como BBDDs de entrada existan en el directorio segmentationFiles
	 * También creará ficheros con TODOS los datos extraidos y que cumplen la segmentación en el directorio segmentationFilesRaw
	 *
	 * @return void
	 */
	public function createNewCounterSegmentation(CounterInputRequest $request, SegmentationCounterInterface $segmentationCounter)
	{


		//Lo primero lo insertamos en la tabla correspondiente.
		$segmentationRequest = new SegmentationCounterRequest();
		$segmentationRequest->uuid_token = Uuid::generate(4)->string;
		$segmentationRequest->request = json_encode($request->all());
		$segmentationRequest->save();
		$counterResponse = $segmentationCounter->processRequest($segmentationRequest);

		return response()->json($counterResponse, 200);

	}

}