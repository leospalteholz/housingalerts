<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::all();
        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug',
            'contact_email' => 'required|email',
            'website_url' => 'nullable|url|max:255',
            'about' => 'nullable|string|max:1000',
        ]);
        Organization::create($validated);
        return redirect()->route('organizations.index')->with('success', 'Organization created successfully!');
    }

    public function show($id)
    {
        $organization = Organization::findOrFail($id);
        return view('organizations.show', compact('organization'));
    }

    public function edit($id)
    {
        $organization = Organization::findOrFail($id);
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug,' . $organization->id,
            'contact_email' => 'required|email',
            'website_url' => 'nullable|url|max:255',
            'about' => 'nullable|string|max:1000',
        ]);
        $organization->update($validated);
        return redirect()->route('organizations.index')->with('success', 'Organization updated successfully!');
    }

    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();
        return redirect()->route('organizations.index')->with('success', 'Organization deleted successfully!');
    }

    // Allow regular admins to edit their own organization
    public function editOwn()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            abort(403, 'You are not associated with any organization.');
        }
        
        return view('organizations.edit', compact('organization'));
    }

    public function updateOwn(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            abort(403, 'You are not associated with any organization.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug,' . $organization->id,
            'contact_email' => 'required|email',
            'website_url' => 'nullable|url|max:255',
            'about' => 'nullable|string|max:1000',
            'areas_active' => 'nullable|string|max:255',
            'user_visible' => 'boolean',
        ]);
        
        $organization->update($validated);
        
        return redirect()->route('dashboard')->with('success', 'Organization updated successfully!');
    }
}
