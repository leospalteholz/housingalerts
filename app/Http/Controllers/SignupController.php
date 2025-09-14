<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class SignupController extends Controller
{
    /**
     * Show the signup form
     */
    public function showSignupForm()
    {
        $organizations = Organization::where('user_visible', true)->orderBy('name')->get();
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
            'password' => 'required|string|min:8|confirmed',
            'organization_id' => 'required|exists:organizations,id,user_visible,1',
            'regions' => 'nullable|array',
            'regions.*' => 'exists:regions,id',
        ]);

        // Additional validation: ensure regions belong to the selected organization
        if ($request->has('regions') && !empty($request->regions)) {
            $validRegions = Region::where('organization_id', $request->organization_id)
                ->whereIn('id', $request->regions)
                ->count();
            
            if ($validRegions !== count($request->regions)) {
                return back()->withErrors(['regions' => 'Selected regions must belong to the chosen organization.'])->withInput();
            }
        }

        // Create the user with the provided password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'organization_id' => $request->organization_id,
            'is_admin' => false,
            'is_superuser' => false,
        ]);

        // Attach selected regions
        if ($request->has('regions')) {
            $user->regions()->attach($request->regions);
        }

        // Send email verification
        event(new Registered($user));

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
