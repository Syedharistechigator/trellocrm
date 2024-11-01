<?php

namespace App\Http\Controllers\Api\Board\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

class ApiClientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $clients = Client::where('status', 1)->get(['id', 'name', 'email']);
            return response()->json(['clients' => ClientResource::collection($clients)], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
