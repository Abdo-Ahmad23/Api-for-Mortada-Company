<?php

namespace App\Http\Controllers\Api;

use App\Http\controllers\ApiResonseTrait;
use App\Http\Controllers\Controller;

use App\Models\Admin;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OwnerController extends Controller
{
    use ApiResonseTrait;

    function login(Request $request){
     // Define validation rules
     $rules = [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
    ];

    try {
        // Validate the request data
        $validatedData = $request->validate($rules);

        if (Auth::guard('owner')->attempt($validatedData)) {
            // Authentication passed, generate a token
            $credentials = $request->only(['email', 'password']);

            $token = Auth::guard('owner')->attempt($credentials);  //generate token
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

        // if($token)
        // {
            // return response()->json([
            //     'status'=>200,
            //     'message'=>'Admin loginned successfully',
            //     'token'=>$token,
            // ]);
        // }
        // return response()->json([
        //     'status'=>422,
        //     'message'=>'Invalid Data',
        //     // 'token'=>$token,

        // ]);
    } catch (ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'message' => 'Validation errors.',
            'errors' => $e->errors(),
            'status'=>422,

        ], 422);
    } catch (\Exception $e) {
        // Handle any other exceptions
        return response()->json([
            'message' => 'An error occurred during login. Please try again.',
            'error' => $e->getMessage(),
            'status'=>500,

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
        Auth::guard('owner')->logout();
        return response()->json([
            'status'=>true,
            'message'=>'Admin was logout successfully'
        ]);
    }

    function addAdmin(Request $request){
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ];

        try {
            // Validate the request data
            $validatedData = $request->validate($rules);

            // Create the admin user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                // 'role' => 'admin',  // Assume you have a role column or similar
            ]);

            // Optionally, send a welcome email or perform other actions

            // Return a success response
            return response()->json([
                'message' => 'Admin added successfully.',
                'data' => $user,
                'status'=>200,
            ], 201);

        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation errors.',
                'errors' => $e->errors(),
                'status'=>422,
            ], 422);
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
                'message' => 'An error occurred while adding the admin. Please try again.',
                'error' => $e->getMessage(),
                'status'=>500,
            ], 500);
        }
    }
    public function removeAdmin($id)
    {
        try {
            // Validate that the ID is a valid format
            if (!is_numeric($id)) {
                return response()->json([
                    'message' => 'Invalid ID format.',
                ], 400);
            }

            // Find the admin by ID
            $admin = Admin::findOrFail($id);



            // Delete the admin
            $admin->delete();

            // Return a success response
            return response()->json([
                'message' => 'Admin removed successfully.',
                'status'=>200,

            ], 200);

        } catch (ModelNotFoundException $e) {
            // Handle model not found error
            return response()->json([
                'message' => 'Admin not found.',
                'status'=>404,

            ], 404);
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
                'message' => 'An error occurred while removing the admin. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    function getAllAdmins()
    {
        try {
            // Retrieve all admins
            $admins = Admin::all();

            // Return a success response with the list of admins
            return response()->json([
                'message' => 'Admins retrieved successfully.',
                'admins' => $admins,
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
                'message' => 'An error occurred while retrieving admins. Please try again.',
                'error' => $e->getMessage(),
                'status'=>500,
                
            ], 500);
        }
    }
    function getAllUsers()
    {
        $users = User::all();
        return $this->apiResponse($users,'',200);

    }


    public function addProject(Request $request)
    {
        if ($request->hasFile('image')) {
            // Get original extension
            $extension = $request->file('image')->getClientOriginalExtension();
            // Generate unique file name
            $imageName = time() . '_' . Str::random(10) . '.' . $extension;
            // Store image
            $imagePath = $request->file('image')->storeAs('projects', $imageName, 'public');
        } else {
            return response()->json(['error' => 'Image not uploaded'], 400);
        }


        $project = Project::create([
            'title' => $request->title,
            'body' => $request->body,
            'image' => $imagePath,
        ]);

        return response()->json($project, 201);
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        // Handle file upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }

            // Get original extension
            $extension = $request->file('image')->getClientOriginalExtension();
            // Generate unique file name
            $imageName = time() . '_' . Str::random(10) . '.' . $extension;
            // Store new image
            $imagePath = $request->file('image')->storeAs('projects', $imageName, 'public');
            $project->image = $imagePath;
        }

        // Update other project details
        $project->name = $request->name;
        $project->description = $request->description;
        $project->save();
    }

    public function removeProject($id)
    {
        $project = Project::findOrFail($id);

        // Delete the image
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return response()->json(['message' => 'Project removed successfully','status'=>200]);
    }
}