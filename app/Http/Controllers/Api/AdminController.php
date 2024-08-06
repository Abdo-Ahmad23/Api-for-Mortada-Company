<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;
class AdminController extends Controller
{

    function login(Request $request){
         // Define validation rules
         $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ];

        try {
            // Validate the request data
            $validatedData = $request->validate($rules);

            // Attempt to log the user in
            if (Auth::guard('admin')->attempt($validatedData)) {
                // Authentication passed, generate a token
                $credentials = $request->only(['email', 'password']);

                $token = Auth::guard('admin')->attempt($credentials);  //generate token

                // Return a success response with the token
                return response()->json([
                    'message' => 'Login successful.',
                    'token' => $token,
                    'status' => 200,
                ], 200);
            } else {
                // Authentication failed
                return response()->json([
                    'message' => 'Invalid email or password.',
                    'status' => 401,

                ], 401);
            }
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation errors.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'message' => 'An error occurred during login. Please try again.',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
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
        Auth::guard('admin')->logout();
        return response()->json([
            'status'=>true,
            'message'=>'Admin was logout successfully'
        ]);
    }

}
