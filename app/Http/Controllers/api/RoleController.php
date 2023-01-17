<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Competitor;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function getRoles(){
        $roles = Role::get();
        return response()->json(['data' => $roles,'success' => true,'status' => 200]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required'
        ]);
        if($validator->fails()) return response()->json(['data' => $validator->errors(),'success' => false,'error' => 500]);

        $role = new Role();
        $role->name = $request->name;
        $role->slug = Str::slug($request->name);
        $role->save();
        return response()->json(['data' => $role->name.' created Successfully','success' => true,'status' => 200]);
    }

    public function update(Request $request,Role $role){
        if(!$role) return response()->json(['data' => 'Role not found','success' => false,'status' => 400]);
        $validator = Validator::make($request->all(),[
            'name' => 'required'
        ]);
        if($validator->fails()) return response()->json(['data' => $validator->errors(),'success' => false,'error' => 500]);

        Competitor::where('role',$role->slug)->update([
            'role' => Str::slug($request->name)
        ]);

        $role->name = $request->name;
        $role->slug = Str::slug($request->name);
        $role->update();

        return response()->json(['data' => $role->name.' updated Successfully','success' => true,'status' => 200]);
    }

    public function delete(Role $role){
        if(!$role) return response()->json(['data' => 'Role not found','success' => false,'status' => 400]);
        Competitor::where('role',$role->slug)->delete();
        $role->delete();
        return response()->json(['data' => $role->name.' deleted successfully','success' => true,'status' => 200]);
    }
}
