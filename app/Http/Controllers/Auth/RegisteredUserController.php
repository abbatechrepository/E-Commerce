<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterCustomerRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $user = User::query()->create([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'role' => UserRole::CUSTOMER,
                'is_active' => true,
            ]);

            Customer::query()->create([
                'user_id' => $user->id,
                'phone' => $request->string('phone')->toString() ?: null,
                'birth_date' => $request->date('birth_date'),
                'marketing_consent' => $request->boolean('marketing_consent'),
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')->with('status', 'Conta criada com sucesso.');
    }
}
