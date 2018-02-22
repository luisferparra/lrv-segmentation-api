<?php

namespace App\Http\Controllers\ApiAuth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;

class PassportController extends Controller
{
    protected $successStatus = 200;
    /**
     * Login Api
     *
     * @return void
     */
    public function login(Request $request)
    {
        
        if (Auth::attempt(['email' => $request['username'], 'password' => $request['password'],'active'=>true])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp',$user->getRoleNames()->toArray())->accessToken;
            $success['token'] = $token;
            
            $fromSwagger = $request->get('grant_type') == 'password';
            //Actualizamos su último loggeo
            $user->last_logged_at = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();
            if ($fromSwagger)
                return response()->json([$token]);
            return response()->json(['sucess' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

    }




    public function register(Request $request)
    {
        //$success = "Claro quesi guapis";
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        
        $input = $request->only(['name']);


        $input['password'] = bcrypt($request->get('password'));
        $input['email'] = $request->get('username');
        
        $user = User::create($input);
        $user->assignRole('SuperAdmin');

        
        //By default, a new user is created assigning a Visitor role
        $success['token'] = $user->createToken('MyApp', $user->getRoleNames()->toArray())->accessToken;
        $success['mame'] = $user->name;
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function getDetails()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
        return $this->successStatus;
    }
}
