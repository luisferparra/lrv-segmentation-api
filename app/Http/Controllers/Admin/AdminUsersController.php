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
use App\Http\Requests\Admin\Users\NewUserRequest;

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
/**
 * Funcón que recibe los datos e inserta el dato en las tablas correspondientes
 *
 * @return void
 */
    public function userNewInsert(NewUserRequest $request) {
		$status = 'success';
        
        $user = new User();
        $user->name = ucwords(trim($request->get('name')));
        $user->email = strtolower(trim($request->get('email')));
        $user->password = bcrypt($request->get('password'));
        $user->save();
        $arrRoles = $request->get('roles');
        foreach ($arrRoles as  $role) {
            $user->assignRole($role);
        }
        $msg = sprintf('Item with #%s has been created', $user->id);
		return redirect()->route('AdminUsersList')->with('status', $status)->with('msg', $msg);
        

    }
}
