<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

class CategoryController extends Controller
{
    public function categories()
    {
        try {
            $categories = Category::all();
            return response()->json(['data' => $categories, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => "fail"]);
            }

            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            return response()->json(['data' => $category, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if (!$category) return response()->json(["data" => "Category not found", "status" => 'error']);

            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => 'fail']);
            }

            $category->update([
                'id' => $category->id,
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            return response()->json(['data' => $category, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function delete(Category $category)
    {
        try {
            if (!$category) return response()->json(["data" => "Category not found", "status" => "error"]);
            $category->delete();
            return response()->json(['data' => $category, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }
}
