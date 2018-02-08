<?php

use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */



Route::get('segmentations', 'SegmentationController@showInfo');
Route::get('segmentations/{api_name}', 'SegmentationController@showValuesSegmentation')->where('api_name', '[a-z0-9\-\.]+');

Route::post('segmentations','SegmentationController@createNewSegmentation');
Route::post('segmentations/{api_name}', 'SegmentationController@createValuesSegmentation')->where('api_name', '[a-z0-9\-\.]+');
Route::put('segmentations/{api_name}/{id}', 'SegmentationController@updateValuesSegmentation')->where('api_name', '[a-z0-9\-\.]+')->where('id','[0-9]+');
Route::delete('segmentations/{api_name}/{id}', 'SegmentationController@deleteValuesSegmentation')->where('api_name', '[a-z0-9\-\.]+')->where('id','[0-9]+');

Route::match(['post','put'],'segmentations/v1/counts','SegmentationController@createNewCounterSegmentation');



//Introducimos aquí las apis para actualización de datos del CRM. el Idchannel siempre tendrá que venir
Route::post('data/loads','SegmentationController@createUsersByIdCRMData');
Route::post('data/loads/{api_name}','SegmentationController@createUsersByApiNameCRMData')->where('api_name', '[a-z0-9\-\.]+');
Route::get('data/loads/info/{uuid_token}','SegmentationController@infoLoadData')->where('uuid_token','^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$');




//Route::get('segmentations/info/{api_name}', 'SegmentationController@showInfo');


Route::middleware('auth:api')->get('/users', function (Request $request) {
	return $request->user();
});
