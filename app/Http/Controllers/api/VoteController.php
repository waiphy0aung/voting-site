<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Competitor;
use App\Models\VoteCompetitor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VoteController extends Controller
{
    public function competitors()
    {
        try {
            $competitors = Competitor::all();
            foreach ($competitors as $key => $value) {
                $competitors[$key]->vote_count = $this->voteDetails($value->id, Auth::id())[0];
                $competitors[$key]->is_vote = $this->voteDetails($value->id, Auth::id())[1];
                $competitors[$key]->loading = false;
            }
            return response()->json(['data' => $competitors, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function competitor(Competitor $competitor)
    {
        try {
            if (!$competitor) return response()->json(['data' => 'Competitor not found', 'status' => 'error']);
            $competitor->vote_count = $this->voteDetails($competitor->id, Auth::id())[0];
            $competitor->is_vote = $this->voteDetails($competitor->id, Auth::id())[1];
            $competitor->loading = false;
            return response()->json(['data' => $competitor, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'no' => 'required',
                'profile' => 'required',
                'height' => 'required',
                'weight' => 'required',
                'age' => 'required',
                'phone' => 'required',
                'location' => 'required',
                'hobby' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => 'fail']);
            }

            $competitor = Competitor::create([
                'name' => $request->name,
                'profile' => $request->profile,
                'number_of_vote' => $request->no,
                'height' => $request->height,
                'weight' => $request->weight,
                'age' => $request->age,
                'phone' => $request->phone,
                'location' => $request->location,
                'hobby' => $request->hobby
            ]);
            $competitor->vote_count = $this->voteDetails($competitor->id, Auth::id())[0];
            $competitor->is_vote = $this->voteDetails($competitor->id, Auth::id())[1];
            $competitor->loading = false;

            return response()->json(['data' => $competitor, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function update(Request $request, Competitor $competitor)
    {
        try {
            if (!$competitor) return response()->json(['data' => 'Miss not found!', 'status' => 'error']);

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'no' => 'required',
                'profile' => 'required',
                'height' => 'required',
                'weight' => 'required',
                'age' => 'required',
                'phone' => 'required',
                'location' => 'required',
                'hobby' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['data' => $validator->errors(), 'status' => 'fail']);
            }

            $competitor->update([
                'id' => $competitor->id,
                'name' => $request->name,
                'profile' => $request->profile,
                'number_of_vote' => $request->no,
                'height' => $request->height,
                'weight' => $request->weight,
                'age' => $request->age,
                'phone' => $request->phone,
                'location' => $request->location,
                'hobby' => $request->hobby
            ]);
            $competitor->vote_count = $this->voteDetails($competitor->id, Auth::id())[0];
            $competitor->is_vote = $this->voteDetails($competitor->id, Auth::id())[1];
            $competitor->loading = false;

            return response()->json(['data' => $competitor, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function delete(Competitor $competitor)
    {
        try {
            if (!$competitor) return response()->json(['data' => 'Miss not found!', 'status' => 'error']);
            $competitor->delete();
            return response()->json(['data' => $competitor, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function voteCompetitor(Request $request)
    {
        try {
            $voter = VoteCompetitor::where('user_id', Auth::id())->where('role', $request->role)->where('competitor_id', $request->competitor_id);
            $voted = VoteCompetitor::where('user_id', Auth::id())->where('role', $request->role);
            $competitor = Competitor::where('id', $request->competitor_id)->first();
            if ($voter->first()) {
                $voter->delete();
                return response()->json(['data' => 'You are unvoted ' . $competitor->name, 'status' => 'success']);
            } else {
                if (!$voted->first()) {
                    $voter->create([
                        'user_id' => Auth::id(),
                        'competitor_id' => $request->competitor_id,
                        'role' => $request->role,
                    ]);
                    return response()->json(['data' => 'You are voted ' . $competitor->name, 'status' => 'success']);
                }
            }
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function voteCompetitors()
    {
        try {
            $voteCompetitors = VoteCompetitor::all();
            return response()->json(['data' => $voteCompetitors, 'status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['data' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function voteDetails($competitor_id, $user_id)
    {
        try {
            $vote_count = VoteCompetitor::where('competitor_id', $competitor_id)->count();
            $user_id = VoteCompetitor::where('user_id', $user_id)->where('competitor_id', $competitor_id)->first();
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
