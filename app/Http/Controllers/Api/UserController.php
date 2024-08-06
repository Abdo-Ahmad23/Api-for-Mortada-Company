<?php

namespace App\Http\Controllers\Api;

use App\Http\controllers\ApiResonseTrait;
use App\Http\Controllers\Controller;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    use ApiResonseTrait;
    function register(Request $request)
    {
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ];

        try {
            // Validate the request data
            $validatedData = $request->validate($rules);

            // Create a new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Return a success response
            return response()->json([
                'message' => 'Registration successful.',
                'user' => $user,
                'status' => 200,

            ], 200);

        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation errors.',
                'errors' => $e->errors(),
                'status' => 400,

            ], 402);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'message' => 'An error occurred during registration. Please try again.',
                'error' => $e->getMessage(),
                'status' => 500,

            ], 500);
        }

    }


    function login(Request $request)
    {
        // Define validation rules
        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ];

        try {
            // Validate the request data
            $validatedData = $request->validate($rules);

            // Attempt to log the user in
            if (Auth::attempt($validatedData)) {
                // Authentication passed, generate a token
                $credentials = $request->only(['email', 'password']);

                $token = Auth::guard('api')->attempt($credentials);  //generate token

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




    function refreshToken()
    {
        $newToken = auth()->refresh();

        return response()->json([
            'status' => true,
            'message' => 'New Access Token was generated',
            'token' => $newToken,

        ]);
    }

    function logout()
    {
        try {
            // Ensure the user is authenticated
            if (Auth::check()) {
                // Revoke all tokens for the authenticated user
                Auth()->logout();

                // Return a success response
                return response()->json([
                    'message' => 'Logout successful.',
                    'status' => 200,
                ], 200);
            } else {
                // If the user is not authenticated, return an error
                return response()->json([
                    'message' => 'User is not authenticated.',
                    'status' => 401,
                ], 401);
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'message' => 'An error occurred during logout. Please try again.',
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAllUsers()
    {
        try {
            // Retrieve all users
            $users = User::all();

            // Return a success response with the list of users
            return response()->json([
                'message' => 'Users retrieved successfully.',
                'data' => $users,
                'status'=>200,
                
            ], 200);

        } catch (QueryException $e) {
            // Handle database errors
            return response()->json([
                'message' => 'Database error occurred.',
                'error' => $e->getMessage(),
                'status'=>500,

            ], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'message' => 'An error occurred while retrieving users. Please try again.',
                'error' => $e->getMessage(),
                'status'=>500,
            ], 500);
        }
    }

}