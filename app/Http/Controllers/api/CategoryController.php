<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Vote;
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
            return response()->json(['data' => $categories, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => "fail"], 500);
            }

            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            return response()->json(['data' => $category, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if (!$category) return response()->json(["data" => "Category not found", "status" => 'error'], 404);

            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => 'fail'], 500);
            }

            $category->update([
                'id' => $category->id,
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            return response()->json(['data' => $category, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function delete(Category $category)
    {
        try {
            if (!$category) return response()->json(["data" => "Category not found", "status" => "error"], 404);
            $category->delete();
            Vote::where('categoryId', $category->id)->delete();
            return response()->json(['data' => $category->id, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function addCategoryData()
    {
        try {
            $arr = json_decode('[{
  "_id": {
    "$oid": "64d8f397a52e22225919674f"
  },
  "name": "Miss Myanmar 2023",
  "slug": "miss-myanmar-2023",
  "createdAt": {
    "$date": "2023-08-13T15:15:35.297Z"
  },
  "updatedAt": {
    "$date": "2023-08-22T07:09:27.309Z"
  },
  "__v": 0
},
{
  "_id": {
    "$oid": "64e1cc06a0e36f5e7c271c75"
  },
  "name": "Miss Healthy Skin",
  "slug": "miss-healthy-skin",
  "createdAt": {
    "$date": "2023-08-20T08:17:10.456Z"
  },
  "updatedAt": {
    "$date": "2023-08-22T07:10:21.162Z"
  },
  "__v": 0
},
{
  "_id": {
    "$oid": "64e1cc13a0e36f5e7c271c78"
  },
  "name": "Miss Beautiful Smile",
  "slug": "miss-beautiful-smile",
  "createdAt": {
    "$date": "2023-08-20T08:17:23.707Z"
  },
  "updatedAt": {
    "$date": "2023-08-20T08:17:23.707Z"
  },
  "__v": 0
},
{
  "_id": {
    "$oid": "64e1cc19a0e36f5e7c271c7b"
  },
  "name": "Best Face",
  "slug": "best-face",
  "createdAt": {
    "$date": "2023-08-20T08:17:29.162Z"
  },
  "updatedAt": {
    "$date": "2023-08-20T08:17:29.162Z"
  },
  "__v": 0
}]');
            foreach ($arr as $key => $value) {
                Category::create([
                    'name' => $arr[$key]->name,
                    'slug' => $arr[$key]->slug
                ]);
            }
            return "success";
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }
}
