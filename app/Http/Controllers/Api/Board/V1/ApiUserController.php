<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class ApiUserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $users = User::whereIn('type', ['lead', 'staff', 'executive', 'pcc'])->where('status', 1)->get(['id', 'name', 'email', 'image', 'type', 'team_key','trello_id']);
            return response()->json(['users' => UserResource::collection($users)], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
