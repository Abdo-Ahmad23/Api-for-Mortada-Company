<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
class ApiController extends Controller
{
    function register(Request $request){
        $validator=$request->validate(
            [
                'name'=>'required',
                'email'=>'required|unique:admins',
                'password'=>'required',

            ]);

            Admin::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ]);

            return response([
                'status'=>true,
                'message'=>'Data was created ',

            ]);


    }


    function login(Request $request){
        $validator=$request->validate([
            'email'=>'required',
            'password'=>'required',

        ]);

        // $token=JWTAuth::attempt([
        //     'email'=>$request->email,
        //     'password'=>$request->password,

        // ]);

        $credentials = $request->only(['email', 'password']);

        $token = Auth::guard('admin')->attempt($credentials);  //generate token


        if($token)
        {
            return response()->json([
                'status'=>true,
                'message'=>'Admin loginned successfully',
                'token'=>$token,
            ]);
        }
        return response()->json([
            'status'=>false,
            'message'=>'Invalid Data',
            // 'token'=>$token,

        ]);
    }


    function profile(){
        $adminData=Auth::guard('admin')->user();

        return response()->json([
            'status'=>true,
            'message'=>'ProfileData',
            'admin'=>$adminData,

        ]);
    }

    function refreshToken(){
        $newToken=auth()->refresh();

        return response()->json([
            'status'=>true,
            'message'=>'New Access Token was generated',
            'token'=>$newToken,

        ]);
    }

    function logout(){
        auth()->logout();
        return response()->json([
            'status'=>true,
            'message'=>'Admin was logout successfully'
        ]);
    }

}