<?php

namespace App\Http\Controllers\api;

use Session;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function getUsers(){
        $user = User::all();
        $arr = [];
        foreach($user as $key => $u){
            $id = $user[$key]->id;
            $voter_id = $user[$key]->voter_id;
            $password = $user[$key]->password;
            $url = ['id' => $id,'voter_id' => $voter_id,'password' => $password];
            array_push($arr,$url);
        }
        return $arr;
    }

    public function Login(Request $request){
      try{
        $validate = Validator::make($request->all(),[
          'email' => 'required|email',
          'password' => 'required'
        ]);
        if($validate->fails()) return response()->json(['data'=>$validate->errors(),'status' => "error"]); 
        $user = User::where('email',$request->email)->first();
        if(!$user) return response()->json(['data' => 'Email or Password Invalid','status' => "fail"]);

        if (Hash::check($request->password, $user->password)) {
            $authUser = $user;
            $token = $authUser->createToken($authUser->email . '_' . now().'_'.$authUser->role);
            return response()->json(['data'=>['token' => $token->accessToken,'user'=> $authUser],'status' => 'success']);
        } else {
            return response()->json(['data' => 'Email or Password Invalid','status' => "fail"]);
        }
      }catch(Exception $e){
          return response()->json(['data' => $e->getMessage(),'status' => "fail"]);
      }
        
    }

     public function Signup(Request $request){
        try{
          $validate = Validator::make($request->all(),[
              'name'=>'required|min:3|max:99|unique:users,name',
              'email' => 'required|email|unique:users,email',
              'password' => 'required|min:4|max:99',
              'password' => 'required|min:4|max:99',
              'confirmPassword' => 'required|same:password',
          ]);
          if($validate->fails()){
             return response()->json(['data'=>$validate->errors(),'status' => "error"]);
          }
          
          $user = new User();
          $user->name = $request->name;
          $user->email = $request->email;
          $user->password = Hash::make($request->password);
          $user->role = 'user';
          $user->save();

          $random = random_int(100000, 999999);
          $token = $user->createToken($user->email . '_' .now().'_'.$user->role);
          $user->update([
              'check_code' => $random
          ]);
          $data = [
                 'random'=>$random,
                 'name' => $user->name,
                 'email' => $user->email,
                 'token' => $token
             ];
          
          return response()->json(['data'=>['token'=>$token->accessToken,'user'=>$user],'status' => "success"]);  
        } catch (Exception $e){
          return response()->json(['data' => $e->getMessage(),'status' => "fail"]);
        }
        

    }

    public function register(Request $request){
        $user = User::create([
            'voter_id' => $request->voter_id,
            'role' => 'user',
            'password' => $request->password,
            'number_of_vote' => $request->no
        ]);
    }



}

