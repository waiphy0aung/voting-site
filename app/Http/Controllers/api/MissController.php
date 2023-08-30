<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Miss;
use App\Models\Vote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MissController extends Controller
{
    public function misses()
    {
        try {
            $misses = Miss::all();
            foreach ($misses as $key => $value) {
                $misses[$key]->voteCount = $this->voteDetails($value->id, Auth::id())[0];
                $misses[$key]->isVote = $this->voteDetails($value->id, Auth::id())[1];
            }
            return response()->json(['data' => $misses, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function miss(Miss $miss)
    {
        try {
            if (!$miss) return response()->json(['data' => 'Miss not found', 'status' => 'error']);
            $miss->voteCount = $this->voteDetails($miss->id, Auth::id())[0];
            $miss->isVote = $this->voteDetails($miss->id, Auth::id())[1];
            $miss->loading = false;
            return response()->json(['data' => $miss, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = json_decode($request->data, true);
            $image = $request->file('image');
            $firebase_storage_path = 'Students/';
            $localfolder = public_path('firebase-temp-uploads') . '/';
            $extension = $image->getClientOriginalExtension();
            $file      = "test" . '.' . $extension;
            if ($image->move($localfolder, $file)) {
                $uploadedfile = fopen($localfolder . $file, 'r');
                app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $firebase_storage_path . $file]);
                unlink($localfolder . $file);
                echo 'success';
                return;
            } else {
                echo 'error';
            }
            /* return response()->json(['data' => collect($data)]); */
            $validator = Validator::make($data, [
                'name' => 'required',
                'image' => 'required',
                'height' => 'required',
                'weight' => 'required',
                'bust' => 'required',
                'waist' => 'required',
                'hips' => 'required',
                'age' => 'required',
                'location' => 'required',
                'hobby' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => 'fail'], 500);
            }

            $miss = Miss::create([
                'name' => $data->name,
                'image' => $data->image,
                'number_of_vote' => $data->no,
                'height' => $data->height,
                'weight' => $data->weight,
                'age' => $data->age,
                'phone' => $data->phone,
                'location' => $data->location,
                'hobby' => $data->hobby
            ]);
            $miss->vote_count = $this->voteDetails($miss->id, Auth::id())[0];
            $miss->is_vote = $this->voteDetails($miss->id, Auth::id())[1];
            $miss->loading = false;

            return response()->json(['data' => $miss, 'status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    public function update(Request $request, Miss $miss)
    {
        try {
            if (!$miss) return response()->json(['data' => 'Miss not found!', 'status' => 'error']);

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'required',
                'height' => 'required',
                'weight' => 'required',
                'bust' => 'required',
                'waist' => 'required',
                'hips' => 'required',
                'age' => 'required',
                'location' => 'required',
                'hobby' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => 'fail']);
            }

            $miss->update([
                'name' => 'required',
                'image' => 'required',
                'height' => 'required',
                'weight' => 'required',
                'bust' => 'required',
                'waist' => 'required',
                'hips' => 'required',
                'age' => 'required',
                'location' => 'required',
                'hobby' => 'required'
            ]);
            $miss->voteCount = $this->voteDetails($miss->id, Auth::id())[0];
            $miss->isVote = $this->voteDetails($miss->id, Auth::id())[1];

            return response()->json(['data' => $miss, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function delete(Miss $miss)
    {
        try {
            if (!$miss) return response()->json(['data' => 'Miss not found!', 'status' => 'error']);
            $miss->delete();
            return response()->json(['data' => $miss, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function voteDetails($miss_id, $user_id)
    {
        try {
            $vote_count = Vote::where('miss_id', $miss_id)->count();
            $user_id = Vote::where('user_id', $user_id)->where('miss_id', $miss_id)->first();
            if ($user_id) {
                $is_like = true;
            } else {
                $is_like = false;
            }
            return [$vote_count, $is_like];
            /* return response()->json(['data' => [$vote_count, $is_like], 'status' => 'success']); */
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }
}
