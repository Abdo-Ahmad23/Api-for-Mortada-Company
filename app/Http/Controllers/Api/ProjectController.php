<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\controllers\ApiResonseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectController extends Controller
{
    use ApiResonseTrait;

    public function addProject(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
        try {
            // Validate the request data
        $validatedData = $request->validate($rules);

        $project = $request->except('image');
        if ($request->hasFile('image')) {
            /// upload file -> change image name ->
            $image = $request->image;
            $oldimagename = $image->getClientOriginalName();
            $newimagename = uniqid() . $oldimagename;
            $image->move('images', $newimagename);

            $imgUrl = "images/$newimagename";
            $project['image'] = $imgUrl;
        }
        $project = Project::create($project);

            // Return a success response
            return response()->json([
                'message' => 'Project added successfully.',
                'project' => $project,
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
                'message' => 'An error occurred while adding the project. Please try again.',
                'error' => $e->getMessage(),
                'status'=>500,
            ], 500);
        }




    }

    public function updateProject(Request $request, $id)
    {
        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
        try {
        $project = Project::findOrFail($id);

        // Handle file upload if a new image is provided
            $project = $request->except('image');
            if ($request->hasFile('image')) {
                /// upload file -> change image name ->
                $image = $request->image;
                $oldimagename = $image->getClientOriginalName();
                $newimagename = uniqid() . $oldimagename;
                $image->move('images', $newimagename);

                $imgUrl = "images/$newimagename";
                $project['image'] = $imgUrl;
            }

            // return ('project.create');
            $mynewproject = Project::find($id);
            $mynewproject->update($project);
         return response()->json([
            'status' => 200,
            'message' => 'Project updated successfully.',
            'data' => $project,

            ]);
        }catch (ValidationException $e) {
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
            'message' => 'An error occurred while updating the project. Please try again.',
            'error' => $e->getMessage(),
            'status'=>500,

        ], 500);
    }


    }

    public function removeProject($id)
    {
        try{
        $project = Project::findOrFail($id);

        // Delete the image
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project removed successfully',
            'status'=>200,

            ]

    );

        } catch (ModelNotFoundException $e) {
            // Handle model not found error
            return response()->json([
                'message' => 'Project not found.',
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
                'message' => 'An error occurred while removing the project. Please try again.',
                'error' => $e->getMessage(),
                'status'=>500,
            ], 500);
        }


    }
}
