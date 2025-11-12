<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization): View
    {
        $isSuperUser = auth()->user()->is_superuser;

        $usersQuery = User::with(['organization'])
            ->orderBy('organization_id')
            ->orderBy('name');

        if (!$isSuperUser) {
            $usersQuery->where('organization_id', $organization->id);
        }

        $users = $usersQuery->get();

        $admins = $users
            ->filter(fn (User $user) => $user->is_admin || $user->is_superuser)
            ->values();

        return view('users.index', [
            'admins' => $admins,
            'organization' => $organization,
            'isSuperUserView' => $isSuperUser,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization): View
    {
        $organizations = auth()->user()->is_superuser
            ? Organization::orderBy('name')->get()
            : collect([$organization]);

        return view('users.create', [
            'organizations' => $organizations,
            'currentOrganization' => $organization,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Organization $organization, Request $request): RedirectResponse
    {
        $isSuperUser = auth()->user()->is_superuser;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'organization_id' => $isSuperUser ? 'nullable|exists:organizations,id' : 'nullable',
        ]);

        // Ensure the new user is tied to the current organization unless explicitly overridden by a superuser.
        if (!$isSuperUser || empty($validated['organization_id'])) {
            $validated['organization_id'] = $organization->id;
        }

        // Hash the password
        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now(); // Auto-verify admin-created users
        $validated['is_admin'] = true;

        // Create the user
        $user = User::create($validated);

        return redirect($this->orgRoute('users.index'))->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, User $user): View
    {
    $user = $this->resolveAccessibleUser($organization, $user->load(['organization']));

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, User $user): View
    {
        $user = $this->resolveAccessibleUser($organization, $user);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Organization $organization, Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = $this->resolveAccessibleUser($organization, $user);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return redirect($this->orgRoute('users.index'))->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, User $user): RedirectResponse
    {
        $user = $this->resolveAccessibleUser($organization, $user);

        if ($user->id === auth()->id()) {
            return redirect($this->orgRoute('users.index'))->with('error', 'You cannot delete your own account.');
        }

    $user->delete();

        return redirect($this->orgRoute('users.index'))->with('success', 'User deleted successfully!');
    }

    private function resolveAccessibleUser(Organization $organization, User $user): User
    {
        if (auth()->user()?->is_superuser) {
            return $user;
        }

        if ($user->organization_id !== $organization->id) {
            abort(404);
        }

        return $user;
    }
}
