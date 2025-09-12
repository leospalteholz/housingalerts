@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('users.index') }}" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition">
            <h2 class="text-xl font-semibold mb-2">Manage Users</h2>
            <p class="text-gray-600">View, add, edit, or remove users.</p>
        </a>
        <a href="{{ route('regions.index') }}" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition">
            <h2 class="text-xl font-semibold mb-2">Manage Regions</h2>
            <p class="text-gray-600">View, add, edit, or remove regions.</p>
        </a>
        <a href="{{ route('hearings.index') }}" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition">
            <h2 class="text-xl font-semibold mb-2">Manage Hearings</h2>
            <p class="text-gray-600">View, add, edit, or remove hearings.</p>
        </a>
    </div>
</div>
@endsection
