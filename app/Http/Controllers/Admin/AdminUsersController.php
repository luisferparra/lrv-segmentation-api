<?php

/**
 * Controlador para gestionar usuarios y sus roles
 */


namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Schema;
use DB;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUsersController extends Controller
{
    /**
     * Función que gestoina el listado de usuarios
     *
     * @return void
     */
    public function show() {
        $users = User::with('roles')->get();
        
      return view('admin.users.list',['data'=>$users]);
    }
/**
 * Función que carga el formulario de Roles existentes
 *
 * @return void
 */
    public function userNew() {
        $roles = Role::all();
        $rolesOut = [];
        foreach ($roles as $role) {
            # code...
            $rolesOut[$role->id] = $role->name; 
        }
        //dd($rolesOut);
        return view('admin.users.userForm',['roles'=>$rolesOut]);

    }
}
