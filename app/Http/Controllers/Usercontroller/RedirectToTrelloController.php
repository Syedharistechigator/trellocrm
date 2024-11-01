<?php

namespace App\Http\Controllers\Usercontroller;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RedirectToTrelloController extends Controller
{
    public function redirectToTrello()
    {
        $user = auth()->user();
        $expirationTime = Carbon::now()->subMinutes(config('sanctum.expiration'));
        $user->tokens()->where('created_at', '<', $expirationTime)->delete();
        $token = $user->createToken('api-token')->plainTextToken;
        return redirect()->away('https://trello.tgcrm.net/board/?login=' . $token . "&cache=" . Str::random());
    }
}


//
