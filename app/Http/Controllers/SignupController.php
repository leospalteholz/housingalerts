<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;

class SignupController extends Controller
{
    /**
     * Show the signup form
     */
    public function showSignupForm()
    {
        $organizations = Organization::orderBy('name')->get();
        return view('auth.signup', compact('organizations'));
    }

    /**
     * Get regions for a specific organization (AJAX)
     */
    public function getRegions(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $regions = Region::where('organization_id', $request->organization_id)
            ->orderBy('name')
            ->get();

        return response()->json($regions);
    }

    /**
     * Handle the signup submission
     */
    public function processSignup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'organization_id' => 'required|exists:organizations,id',
            'regions' => 'nullable|array',
            'regions.*' => 'exists:regions,id',
        ]);

        // Generate a random password
        $password = Str::random(12);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'organization_id' => $request->organization_id,
            'is_admin' => false,
            'is_superuser' => false,
        ]);

        // Attach selected regions
        if ($request->has('regions')) {
            $user->regions()->attach($request->regions);
        }

        // Send password reset link so user can set their own password
        event(new Registered($user));
        Password::sendResetLink($request->only('email'));

        return redirect()->route('signup.thankyou');
    }

    /**
     * Show thank you page
     */
    public function showThankYou()
    {
        return view('auth.signup-thankyou');
    }
}
