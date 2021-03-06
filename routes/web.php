<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', 'Admin\AdminHomeController@home');

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

/**
 * Rutas ADMIN
 */
Route::middleware(['auth'])->group(function () {
	Route::get('/home', 'Admin\AdminHomeController@index')->name('AdminDashboard');

	Route::get('/admin/fields', 'Admin\AdminDataController@fieldsIndex')->name('AdminFieldsIndex');
	/*Route::get('/admin/fields/edit/{field}', 'Admin\AdminDataController@fieldsEdit')
		->where('field', '[0-9]+')->name('AdminFieldsEdit');*/
	Route::get('/admin/fields/new', 'Admin\AdminDataController@fieldsNew')->name('AdminFieldsNew');
	Route::post('/admin/fields/new', 'Admin\AdminDataController@fieldNewInsert')->name('AdminFieldNewPost');
	Route::get('/admin/fields/edit/{tableControl}', 'Admin\AdminDataController@fieldsEdit')
		->where('tableControl', '[0-9]+')->name('AdminFieldEdit');
	Route::post('/admin/fields/edit/{tableControl}', 'Admin\AdminDataController@fieldsUpdate')
		->where('tableControl', '[0-9]+')->name('AdminFieldUpdatePost');
	Route::get('/admin/fields/remove/{tableControl}', 'Admin\AdminDataController@fieldsRemove')->where('tableControl', '[0-9]+')->name('AdminFieldsRemove');

	Route::get('/admin/segmentation-data/{tableControl}', 'Admin\AdminDataController@valuesIndex')->where('tableControl', '[0-9]+')->name('AdminValuesIndex');
	Route::get('/admin/segmentation-data/{tableControl}/new/', 'Admin\AdminDataController@valuesNew')->where('tableControl', '[0-9]+')->name('AdminValuesNew');
	Route::post('/admin/segmentation-data/{tableControl}/new/', 'Admin\AdminDataController@valuesNewInsert')->name('AdminValuesNewPost');
	
	Route::get('/admin/segmentation-data/{tableControl}/edit/{valueId}', 'Admin\AdminDataController@valuesEdit')->where('tableControl', '[0-9]+')->where('valueId', '[0-9]+')->name('AdminValuesEdit');
	Route::post('/admin/segmentation-data/{tableControl}/edit/{valueId}', 'Admin\AdminDataController@valuesEditPost')->where('tableControl', '[0-9]+')->where('valueId', '[0-9]+')->name('AdminValuesEditPost');

	Route::get('/admin/users','Admin\AdminUsersController@show')->name('AdminUsersList');
	Route::get('/admin/users/new','Admin\AdminUsersController@userNew')->name('AdminUsersNew');
	Route::post('/admin/users/new','Admin\AdminUsersController@userNewInsert')->name('AdminUsersNewPost');
	Route::get('/admin/users/edit/{user}','Admin\AdminUsersController@userEdit')->where('user','[0-9]+')->name('AdminUserEdit');
	Route::post('/admin/users/edit/{user}','Admin\AdminUsersController@userEditPost')->where('user','[0-9]+')->name('AdminUserEditPost');
	Route::get('/admin/users/remove/{user}','Admin\AdminUsersController@userRemove')->where('user','[0-9]+')->name('AdminUserRemove');
	Route::get('/admin/users/activate/{user}','Admin\AdminUsersController@userActivate')->where('user','[0-9]+')->name('AdminUserActivate');
	Route::get('/admin/users/deactivate/{user}','Admin\AdminUsersController@userDeactivate')->where('user','[0-9]+')->name('AdminUserDeactivate');
	
	
	
	

});
