@props([
    'name' => 'info-circle',
    'class' => 'w-5 h-5',
    'inline' => false
])

@php
    $iconPath = resource_path("icons/{$name}.svg");
    $iconExists = file_exists($iconPath);
    
    // Default classes
    $classes = $class;
    
    // Add inline class if needed
    if ($inline) {
        $classes .= ' inline';
    }
@endphp

@if($iconExists)
    <span class="{{ $classes }}" {{ $attributes }}>
        {!! file_get_contents($iconPath) !!}
    </span>
@else
    <!-- Fallback if icon doesn't exist -->
    <span class="{{ $classes }} text-gray-400" {{ $attributes }} title="Icon '{{ $name }}' not found">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </span>
@endif
