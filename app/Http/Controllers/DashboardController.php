<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        // Route users to appropriate dashboard based on their role
        if (auth()->user()->is_superuser || auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
}
