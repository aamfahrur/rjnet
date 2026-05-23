<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function showRegistrationForm(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'username'  => $validated['email'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'password'  => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole('customer');

        event(new Registered($user));
        auth()->login($user);

        return redirect()->route('customer.dashboard');
    }
}
