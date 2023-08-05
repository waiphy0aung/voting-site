<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Competitor;
use App\Models\VoteCompetitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoteController extends Controller
{
    public function competitors(Request $request)
    {
        $competitors = Competitor::all();
        foreach ($competitors as $key => $value) {
            $competitors[$key]->vote_count = $this->voteDetails($value->id, $request->user_id)[0];
            $competitors[$key]->is_vote = $this->voteDetails($value->id, $request->user_id)[1];
            $competitors[$key]->loading = false;
        }
        return $competitors;
    }

    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'no' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors(), 'success' => false]);
        }

        $competitor = Competitor::create([
            'name' => $request->name,
            'profile' => $request->photo,
            'number_of_vote' => $request->no
        ]);

        return response()->json(['data' => 'created successfully', 'sucess' => true]);
    }

    public function update(Request $request, Competitor $competitor)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'no' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['data' => $validator->errors(), 'success' => false]);
        }

        $competitor->update([
            'id' => $request->id,
            'name' => $request->name,
            'profile' => $request->photo,
            'number_of_vote' => $request->no
        ]);

        return response()->json(['data' => 'updated successfully', 'sucess' => true]);
    }

    public function delete(Competitor $competitor)
    {
        $competitor->delete();
        return response()->json(['data' => 'deleted successfully', 'success' => true]);
    }

    public function voteCompetitor(Request $request)
    {
        $voter = VoteCompetitor::where('user_id', $request->user_id)->where('role', $request->role)->where('competitor_id', $request->competitor_id);
        $voted = VoteCompetitor::where('user_id', $request->user_id)->where('role', $request->role);
        $competitor = Competitor::where('id', $request->competitor_id)->first();
        if ($voter->first()) {
            $voter->delete();
            return response()->json(['data' => 'You are unvoted ' . $competitor->name, 'success' => false]);
        } else {
            if (!$voted->first()) {
                $voter->create([
                    'user_id' => $request->user_id,
                    'competitor_id' => $request->competitor_id,
                    'role' => $request->role,
                ]);
                return response()->json(['data' => 'You are voted ' . $competitor->name, 'success' => true]);
            }
        }
    }

    public function voteCompetitors()
    {
        return VoteCompetitor::all();
    }

    public function voteDetails($competitor_id, $user_id)
    {
        $vote_count = VoteCompetitor::where('competitor_id', $competitor_id)->count();
        $user_id = VoteCompetitor::where('user_id', $user_id)->where('competitor_id', $competitor_id)->first();
        if ($user_id) {
            $is_like = true;
        } else {
            $is_like = false;
        }
        return [$vote_count, $is_like];
    }
}
