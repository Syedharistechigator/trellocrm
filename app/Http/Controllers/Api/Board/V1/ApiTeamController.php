<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Traits\TeamTrait;
use Illuminate\Http\Request;

class ApiTeamController extends Controller
{
    use TeamTrait;

    public function index(): ?\Illuminate\Http\JsonResponse
    {
        try {
            $teamData = $this->team_trait();
            return response()->json(['teams' => $teamData['teams_resource'],], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }
}
