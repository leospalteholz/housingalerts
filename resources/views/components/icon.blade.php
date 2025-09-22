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
    @php
        $iconContent = file_get_contents($iconPath);
        // Add classes to the SVG element itself
        $iconContent = preg_replace('/<svg/', '<svg class="' . $classes . '"', $iconContent);
        // Add any additional attributes
        $attributeString = $attributes->toHtml();
        if ($attributeString) {
            $iconContent = preg_replace('/<svg/', '<svg ' . $attributeString, $iconContent);
        }
    @endphp
    {!! $iconContent !!}
@else
    <!-- Fallback if icon doesn't exist -->
    <svg class="{{ $classes }} text-gray-400" {{ $attributes }} fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Icon '{{ $name }}' not found">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
@endif
