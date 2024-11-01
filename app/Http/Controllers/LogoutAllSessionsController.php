<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutAllSessionsController extends Controller
{
    public function logoutAllSessions(Request $request)
    {
        $user = Auth::user();
        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        Session::flush();
        Auth::logout();

        return redirect('/login');
    }
}
