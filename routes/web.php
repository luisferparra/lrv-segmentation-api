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

Route::get('/', function () {
	return view('welcome');
});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

/**
 * Rutas ADMIN
 */
Route::middleware(['auth'])->group(function () {
	Route::get('/home', 'Admin\AdminHomeController@index')->name('AdminDashboard');

	Route::get('/admin/fields', 'Admin\AdminDataController@fieldsIndex')->name('AdminFieldsIndex');
	Route::get('/admin/fields/edit/{field}', 'Admin\AdminDataController@fieldsEdit')
		->where('field', '[0-9]+')->name('AdminFieldsEdit');
	Route::get('/admin/fields/new', 'Admin\AdminDataController@fieldsNew')->name('AdminFieldsNew');
	Route::post('/admin/fields/new', 'Admin\AdminDataController@fieldNewInsert');

});
