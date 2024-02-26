<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $tags = Tag::paginate(15);
        return apiResponse('success', $tags);
    }


    public function store(Request $request)
    {
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'name' => "required|unique:tags,name"
                ]
            );

            if ($validate->fails()) {
                return errorApi('validation error', 401, $validate->errors());
            }
            Tag::create($validate->validated());
            return successApi('Create Tag Successfuly');
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $validate = Validator::make(
                $request->all(),
                [
                    'name' => "required|unique:tags,name,$id",
                ]
            );

            if ($validate->fails()) {
                return errorApi('validation error', 401, $validate->errors());
            }
            $tag->update($validate->validated());
            return successApi('Update Tag Successfuly');
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return successApi('Delete Tag Successfuly');
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }
}
