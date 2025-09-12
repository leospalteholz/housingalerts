<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HearingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hearings = \App\Models\Hearing::all();
        return view('hearings.index', compact('hearings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hearings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $hearing = new \App\Models\Hearing();
        $hearing->title = $validated['title'];
        $hearing->body = $validated['body'];
        $hearing->save();

        return redirect()->route('hearings.index')->with('success', 'Hearing created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        return view('hearings.show', compact('hearing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        return view('hearings.edit', compact('hearing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $hearing = \App\Models\Hearing::findOrFail($id);
        $hearing->title = $validated['title'];
        $hearing->body = $validated['body'];
        $hearing->save();

        return redirect()->route('hearings.index')->with('success', 'Hearing updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        $hearing->delete();
        return redirect()->route('hearings.index')->with('success', 'Hearing deleted successfully!');
    }
}
