<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials, remember: true)) {
            return back()->withErrors([
                'email' => 'As credenciais informadas não conferem.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();
        $user->update(['last_login_at' => now()]);

        $intendedFallback = $user->isAdmin()
            ? route('admin.dashboard')
            : route('customer.dashboard');

        return redirect()->intended($intendedFallback)->with('status', 'Login realizado com sucesso.');
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('storefront.home')->with('status', 'Sessão encerrada.');
    }
}
