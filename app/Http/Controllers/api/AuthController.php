<?php

namespace App\Http\Controllers\api;

use Session;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    public function getUsers(User $user)
    {
        try {
            if ($user->id) {
                return response()->json(['data' => $user, 'status' => 'success'], 200);
            }
            $user = User::with("votes")->get();
            return response()->json(['data' => $user, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => "fail"], 500);
        }
    }

    public function deleteUser(User $user)
    {
        try {
            if(!$user) return response()->json(['data' => 'User not found','status' => 'success'],404);
            $user->delete();
            Vote::where('userId',$user->id)->delete();
            return response()->json(['data' => $user->id, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => "fail"], 500);
        }
    }

    public function Login(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if ($validate->fails()) return response()->json(['data' => $validate->errors(), 'status' => "fail"], 500);
            $user = User::where('email', $request->email)->first();
            if (!$user) return response()->json(['data' => 'Email or Password Invalid', 'status' => "error"], 500);

            if (Hash::check($request->password, $user->password)) {
                $authUser = $user;
                $token = $authUser->createToken($authUser->email . '_' . now() . '_' . $authUser->role);
                return response()->json(['data' => ['token' => $token->accessToken, 'user' => $authUser, 'role' => $authUser->role], 'status' => 'success'], 200);
            } else {
                return response()->json(['data' => 'Email or Password Invalid', 'status' => "error"], 500);
            }
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => "fail"], 500);
        }
    }

    public function Signup(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required|min:3|max:99|unique:users,name',
                'email' => 'required|email|unique:users,email',
                /* 'password' => 'required|min:8|letters|mixedCase|numbers|symbols|uncompromised', */
                'password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()],
                'confirmPassword' => 'required|same:password',
            ]);
            if ($validate->fails()) {
                return response()->json(['data' => $validate->errors(), 'status' => "fail"], 500);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->profile = $request->profile;
            $user->password = Hash::make($request->password);
            $user->save();

            $random = random_int(100000, 999999);
            $token = $user->createToken($user->email . '_' . now() . '_' . $user->role);
            $user->update([
                'check_code' => $random
            ]);
            return response()->json(['data' => ['token' => $token->accessToken, 'user' => $user], 'status' => "success"], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => "error"], 500);
        }
    }

    public function LoginWithToken(Request $request)
    {
        try {
            $authUser = User::find(Auth::id());
            $token = $authUser->createToken($authUser->email . '_' . now() . '_' . $authUser->role);
            return response()->json(['data' => ['token' => $token->accessToken, 'user' => $authUser, 'role' => $authUser->role], 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => "error"], 500);
        }
    }
}
