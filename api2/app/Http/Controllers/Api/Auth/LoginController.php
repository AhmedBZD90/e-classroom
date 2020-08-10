<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use Spatie\Permission\Models\Role;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $creds = $request->only(['email', 'password']);

        if (! $token = auth()->attempt($creds)){
            return response()->json(['error' => 'Incorrect Email/Password'], 401);
        }

        $user = User::where('email',$request['email']) -> first();
        $userRole = $user->roles->pluck('name');
        $role = preg_replace('/[^A-Za-z0-9\-]/', '', $userRole);
        $user_id = $user->id;

        return response()->json(['access_token' => $token, 'role' => $role, "user_id" => $user_id]);
    }

    public function refresh()
    {
        try{
            $newToken = auth()->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        return response()->json(['token' => $newToken]);
    }

    public function getAuthUser(Request $request){
        $token = $request['token'];
        $user = JWTAuth::toUser($token);
       return response()->json($user);
    }
}
