<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminHomeController extends Controller {
	//
	/**
	 * Función del dashboard del admin
	 * @return [type] [description]
	 */
	public function index() {
		return view('admin.dashboard');
	}
}
