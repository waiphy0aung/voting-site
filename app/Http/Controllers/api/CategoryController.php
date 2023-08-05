<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function categories(Request $request){
        return response()->json(['data'=>Category::all(),'success'=>true]);
    }

    public function create(Request $request){
        
        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['data'=>$validator->errors(),'success'=>false]);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json(['data' => 'created successfully','sucess' => true]);
    }

    public function update(Request $request,Category $category){
        
        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);
        if(!$category) return response()->json(["data" => "Category not found","success" => false,"status" => 404]);

        if($validator->fails()){
            return response()->json(['data'=>$validator->errors(),'success'=>false]);
        }

        $category->update([
            'id' => $category->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return response()->json(['data' => 'updated successfully','sucess' => true]);
    }

    public function delete(Category $category){
        if(!$category) return response()->json(["data" => "Category not found","success" => false,"status" => 404]);
        $category->delete();
        return response()->json(['data' => 'deleted successfully','success' => true]);
    }
}
