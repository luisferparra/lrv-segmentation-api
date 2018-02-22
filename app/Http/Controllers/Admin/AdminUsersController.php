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
use Illuminate\Support\Facades\Auth;


class AdminUsersController extends Controller
{
    /**
     * Función que gestoina el listado de usuarios
     *
     * @return void
     */
    public function show()
    {
        $users = User::with('roles')->get();

        return view('admin.users.list', ['data' => $users]);
    }
    /**
     * Función que carga el formulario de Roles existentes
     *
     * @return void
     */
    public function userNew()
    {
/*         $roles = Role::all();
        $rolesOut = [];
        foreach ($roles as $role) {
            # code...
            $rolesOut[$role->name] = $role->name; 
        } */
        //dd($rolesOut);
        return view('admin.users.userForm', ['roles' => $this->__getRoles(), 'disableAttr' => []]);

    }
    /**
     * Funcón que recibe los datos e inserta el dato en las tablas correspondientes
     *
     * @return void
     */
    public function userNewInsert(NewUserRequest $request)
    {
        $status = 'success';

        $user = new User();
        $user->name = ucwords(trim($request->get('name')));
        $user->email = strtolower(trim($request->get('email')));
        $user->password = bcrypt($request->get('password'));
        $user->active = (bool)$request->get('active');
        $user->save();
        $arrRoles = $request->get('roles');
        foreach ($arrRoles as $role) {
            $user->assignRole($role);
        }
        $msg = sprintf('Item with #%s has been created', $user->id);
        return redirect()->route('AdminUsersList')->with('status', $status)->with('msg', $msg);


    }

    /**
     * Función pública que devuelve los datos en la edición y rellenar el formulario
     *
     * @param User $user
     * @return void
     */
    public function userEdit(User $user)
    {
       
        
        //Con disablednos aseguramos que el usuario principal no sea eliminado
        $arrDisabled = [];
        if ($user->email == env('APP_GOD') || !$this->__canRemoveSuperAdmin($user)) {
            $arrDisabled = ['disabled' => 'disabled'];
        }
        return view('admin.users.userForm', ['userData' => $user, 'userRoles' => $user->getRoleNames(), 'roles' => $this->__getRoles(), 'disableAttr' => $arrDisabled]);
    }
    /**
     * Función pública que ACTUALIZA LOS DATOS modificados del usuarios
     *
     * @param User $user
     * @return void
     */
    public function userEditPost(NewUserRequest $request, User $user)
    {
        $status = 'success';

        $pwd = $request->get('password');
        if (!empty($pwd))
            $user->password = bcrypt(trim($pwd));
        $user->name = ucwords(strtolower(trim($request->get('name'))));
        $user->email = strtolower(trim($request->get('email')));
        $user->active = (bool)$request->get('active');
//Primero quitamos todos los roles asignados al usuario
        $roles = $user->getRoleNames();
        foreach ($roles as $role) {
    # code...
            $user->removeRole($role);

        }

//Ahoraañadimos los nuevos
        $arrRoles = $request->get('roles');
        foreach ($arrRoles as $role) {
            $user->assignRole($role);
        }
        $user->save();
        $msg = sprintf('Item with #%s has been updated', $user->id);
        return redirect()->route('AdminUsersList')->with('status', $status)->with('msg', $msg);

    }

    /**
     * Función p´ública que elimina un usuario del sistema
     * @param User $user
     * @return void
     */
    public function userRemove(User $user)
    {
        $status = 'success';
        $email = $user->email;
        if ($email == env('APP_GOD')) {
            $status = 'error';
            $msg = 'This User cannot be removed';
            return redirect()->route('AdminUsersList')->with('status', $status)->with('msg', $msg);

        } elseif (!$this->__canRemoveSuperAdmin($user)) {
            $status = 'error';
            $msg = 'Users with SuperAdmin Role cannot be removed. Only SuperAdmin can be removed by SuperAdmin';
            return redirect()->route('AdminUsersList')->with('status', $status)->with('msg', $msg);
        }
        
        
        //Lo primero,es borrar todos sus roles asignados
        $roles = $user->getRoleNames();
        foreach ($roles as $role) {
    # code...
            $user->removeRole($role);

        }
        $msg = sprintf('Item with #%s has been removed', $user->id);
        
        //Y ahora le borramos
        $user->delete();
        return redirect()->route('AdminUsersList')->with('status', $status)->with('msg', $msg);
    }

    /**
     * Función públicaque activa un usuario
     *
     * @param User $user
     * @return void
     */
    public function userActivate(User $user)
    {
        $user->active = true;
        $user->save();
        $msg = sprintf('Item with #%s has been Activated', $user->id);
        return redirect()->route('AdminUsersList')->with('status', 'success')->with('msg', $msg);
    }

    /**
     * Función públicaque Desactiva un usuario
     *
     * @param User $user
     * @return void
     */
    public function userDeactivate(User $user)
    {
        $user->active = false;
        $user->save();
        $msg = sprintf('Item with #%s has been Deactivated', $user->id);
        return redirect()->route('AdminUsersList')->with('status', 'success')->with('msg', $msg);
    }

    /**
     * Función que devuelve un listado con los roles
     *
     * @return array asociativo key=>value
     */
    protected function __getRoles()
    {
        $roles = Role::all();
        $rolesOut = [];
        foreach ($roles as $role) {
            # code...
            $rolesOut[$role->name] = $role->name;
        }
        return $rolesOut;
    }

    /**
     * Función que dice si el usuario logueado puede eliminar o cambiar permisos a un SuperAdmin
     * Si es SuperAdmin, puede hacerlo
     * Si Tiene un role de superadmin el usuario a modificar, no se puede (solo superadmin pueden hacerlo)
     *
     * @param User $user
     * @return void
     */
    protected function __canRemoveSuperAdmin(&$user)
    {
        $userAuth = Auth::user();
        if ($user->email == env('APP_GOD') || $userAuth->hasRole('SuperAdmin'))
            return true;
        if ($user->hasRole('SuperAdmin'))
            return false;
        return true;

    }
}
