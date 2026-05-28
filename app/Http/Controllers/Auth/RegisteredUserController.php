<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    const GUIDE_SECRET_KEY = '12313883';

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'user');

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:user,guide'],
        ];

        if ($role === 'guide') {
            $rules['guide_secret_key']  = ['required', 'string'];
            $rules['guide_specialty']   = ['nullable', 'string', 'max:200'];
            $rules['guide_phone']       = ['nullable', 'string', 'max:20'];
            $rules['guide_experience']  = ['nullable', 'string', 'max:5'];
        }

        $request->validate($rules);

        // Validate secret key for guides
        if ($role === 'guide' && $request->guide_secret_key !== self::GUIDE_SECRET_KEY) {
            throw ValidationException::withMessages([
                'guide_secret_key' => 'Invalid secret key. Please contact the administrator to get a valid key.',
            ]);
        }

        $userData = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $role,
        ];

        if ($role === 'guide') {
            $userData['guide_status']     = 'pending';
            $userData['guide_specialty']  = $request->guide_specialty;
            $userData['guide_phone']      = $request->guide_phone;
            $userData['guide_experience'] = $request->guide_experience;
        }

        $user = User::create($userData);
        event(new Registered($user));

        // Guides are NOT logged in – redirect to a pending page
        if ($role === 'guide') {
            return redirect()->route('login')
                ->with('guide_pending', 'Your guide application has been submitted! We will notify you once an admin reviews and approves your account.');
        }

        Auth::login($user);
        return redirect(route('dashboard', absolute: false));
    }
}

