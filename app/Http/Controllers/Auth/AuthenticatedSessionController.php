<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Branch;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $branches = Branch::all();
        return view('auth.login', compact('branches'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // If logged in with remember, set session to never expire
        if (Auth::viaRemember()) {
            // Set session lifetime to 1 year for remembered users
            $request->session()->put('_session_lifetime', 525600); // 1 year in minutes
        }

        // Store branch information in the session after successful authentication:
        $request->session()->put('branch_id', $request->branch_id);
        $branch = Branch::find($request->branch_id);
        $request->session()->put('branch_name', $branch->name);

        // Store login method for debugging
        $request->session()->put('login_method', Auth::viaRemember() ? 'remember_token' : 'credentials');
        $request->session()->put('login_time', now()->format('Y-m-d H:i:s'));

        // Add welcome back message only if logged in via remember token
        if (Auth::viaRemember()) {
            $request->session()->flash('welcome_back', 'Welcome back! You\'re still logged in from your last session.');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
