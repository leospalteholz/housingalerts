<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // If superuser, can see all users across organizations
        // If admin, can only see users within their organization
        if (auth()->user()->is_superuser) {
            $users = \App\Models\User::with(['organization', 'regions'])->get();
        } else {
            $users = \App\Models\User::with(['organization', 'regions'])
                          ->where('organization_id', auth()->user()->organization_id)
                          ->get();
        }
        
        // Separate admins and regular users
        $admins = $users->filter(function ($user) {
            return $user->is_admin || $user->is_superuser;
        });
        
        $regularUsers = $users->filter(function ($user) {
            return !$user->is_admin && !$user->is_superuser;
        });
        
        return view('users.index', compact('admins', 'regularUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get organizations for superusers
        if (auth()->user()->is_superuser) {
            $organizations = \App\Models\Organization::orderBy('name')->get();
            $regions = \App\Models\Region::with('organization')->orderBy('name')->get();
        } else {
            $organizations = collect();
            $regions = \App\Models\Region::where('organization_id', auth()->user()->organization_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('users.create', compact('organizations', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'required|boolean',
            'organization_id' => auth()->user()->is_superuser ? 'required|exists:organizations,id' : 'nullable',
            'regions' => 'nullable|array',
            'regions.*' => 'exists:regions,id',
        ]);

        // Set organization_id based on user role
        if (!auth()->user()->is_superuser) {
            $validated['organization_id'] = auth()->user()->organization_id;
        }

        // Hash the password
        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now(); // Auto-verify admin-created users

        // Create the user
        $user = \App\Models\User::create($validated);

        // Attach regions if provided
        if (!empty($validated['regions'])) {
            $user->regions()->sync($validated['regions']);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = \App\Models\User::with('regions')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
