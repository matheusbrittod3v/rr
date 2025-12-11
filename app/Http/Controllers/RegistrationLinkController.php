<?php

namespace App\Http\Controllers;

use App\Models\RegistrationLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegistrationLinkController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm($token)
    {
        $link = RegistrationLink::where('token', $token)->firstOrFail();

        if (!$link->isValid()) {
            abort(410, 'This registration link has expired or reached its usage limit.');
        }

        return view('auth.register-link', ['token' => $token]);
    }

    /**
     * Handle registration
     */
    public function register(Request $request, $token)
    {
        $link = RegistrationLink::where('token', $token)->firstOrFail();

        if (!$link->isValid()) {
            return back()->withErrors(['token' => 'This registration link has expired or reached its usage limit.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_provider' => $link->is_provider,
        ]);

        // Assign roles
        if ($link->roles) {
            $user->roles()->sync($link->roles);
        }

        // Increment link usage
        $link->incrementUsage();

        // Log the user in
        auth()->login($user);

        return redirect('/admin')->with('success', 'Registration successful! Welcome aboard.');
    }
}
