<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Route users to appropriate dashboard based on their role
        if (auth()->user()->is_superuser || auth()->user()->is_admin) {
            return redirect($this->orgRoute('admin.dashboard'));
        } else {
            return redirect($this->orgRoute('user.dashboard'));
        }
    }
}
