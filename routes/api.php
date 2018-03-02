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

/*
//Spatie

Route::get('/domains', 'DomainsController@index')
        ->middleware(['can:view-domains'])
        ->name('domains');

    Route::get('/domains/assign/{domain}', 'DomainsController@assignForm')
        ->middleware(['role:super-admin', 'permission:domain-operations'])
        ->name('domains-assign');
Users: SuperAdmin, DataManagement, Segmentator, Visitor

		//
    ->middleware(['auth:api', 'scopes:admin'])
*/
Route::get('segmentations', 'SegmentationController@showInfo')->middleware(['auth:api', 'scope:SuperAdmin,Visitor']);
//Route::get('segmentations', 'SegmentationController@showInfo');
Route::get('segmentations/{api_name}', 'SegmentationController@showValuesSegmentation')->where('api_name', '[a-z0-9\-\.]+')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement,Segmentator'])*/;

Route::post('segmentations','SegmentationController@createNewSegmentation')->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement']);
Route::post('segmentations/{api_name}', 'SegmentationController@createValuesSegmentation')->where('api_name', '[a-z0-9\-\.]+')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement'])*/;
Route::put('segmentations/{api_name}/{id}', 'SegmentationController@updateValuesSegmentation')->where('api_name', '[a-z0-9\-\.]+')->where('id','[0-9]+')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement'])*/;
Route::delete('segmentations/{api_name}/{id}', 'SegmentationController@deleteValuesSegmentation')->where('api_name', '[a-z0-9\-\.]+')->where('id','[0-9]+')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement'])*/;

Route::match(['post','put'],'segmentations/v1/counts','SegmentationController@createNewCounterSegmentation')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement,Segmentator'])*/;



//Introducimos aquí las apis para actualización de datos del CRM. el Idchannel siempre tendrá que venir
Route::post('data/loads','SegmentationController@createUsersByIdCRMData')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement'])*/;
Route::post('data/loads/{api_name}','SegmentationController@createUsersByApiNameCRMData')->where('api_name', '[a-z0-9\-\.]+')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement'])*/;
Route::get('data/loads/info/{uuid_token}','SegmentationController@infoLoadData')->where('uuid_token','^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement'])*/;


// Route::get('data/data-bases/{id}','SegmentationController@getDataBaseList');
Route::get('data/data-bases/list/{status?}','SegmentationController@getDataBaseList')->where("status", "^(|all)$")/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement'])*/;
Route::post('data/data-bases','SegmentationController@postDataBaseList')/*->middleware(['auth:api', 'scopes:SuperAdmin,DataManagement,Segmentator'])*/;
Route::put('data/data-bases/{idbbdd}/{action}','SegmentationController@putDataBaseListActivateDeactivate')->where('idbbdd','[0-9]+')->where('action','[a-z]+')/*->middleware(['auth:api', 'scopes:SuperAdmin'])*/;
Route::put('data/data-bases/{idbbdd}','SegmentationController@putDataBaseList')->where('idbbdd','[0-9]+')/*->middleware(['auth:api', 'scopes:SuperAdmin'])*/;

Route::match(['post','put'],'data/users/{idbbdd}/{action}','SegmentationController@putActivateDeactivateUsersInDB')->where('idbbdd','[0-9]+')->where('action','[a-z]+')/*->middleware(['auth:api', 'scopes:SuperAdmin'])*/;





//Route::get('segmentations/info/{api_name}', 'SegmentationController@showInfo');

Route::post('login','ApiAuth\PassportController@login');
Route::post('token','ApiAuth\PassportController@token');
Route::post('register','ApiAuth\PassportController@register');

Route::group(['middleware'=>'auth:api'],function() {
	Route::post('get-details','ApiAuth\PassportController@getDetails');
});

/* Route::middleware('auth:api')->get('/users', function (Request $request) {
	return $request->user();
}); */