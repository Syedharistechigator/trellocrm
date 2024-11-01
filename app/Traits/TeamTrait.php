<?php

namespace App\Traits;

use App\Http\Resources\TeamResource;
use App\Models\Team;

trait TeamTrait
{
    public function team_trait($type = 'executive')
    {
        $all_teams = Team::where('status', 1)->get();
        $available_teams = auth()->user()->type === $type ? $all_teams :  collect();
        return [
            'teams' => $available_teams,
            'teams_resource' => TeamResource::collection($available_teams),
            'all_teams' => $all_teams,
            'all_teams_resource' => TeamResource::collection($all_teams),
        ];
    }
}
