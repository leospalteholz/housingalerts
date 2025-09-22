{{-- Image Upload Component --}}
@props([
    'name' => 'image',
    'label' => 'Header Image',
    'currentImage' => null,
    'required' => false,
    'helpText' => 'Upload a landscape image (recommended: 1200x400px) to make your hearing more visually appealing. Supports JPEG and WebP formats up to 2MB.'
])

<div class="mb-6">
    <label class="block text-gray-700 font-semibold mb-2{{ $required ? ' required-field' : '' }}">
        {{ $label }}
    </label>
    
    <!-- Current Image Preview (for edit form) -->
    @if($currentImage)
        <div class="mb-4">
            <div class="text-sm text-gray-600 mb-2">Current image:</div>
            <img src="{{ $currentImage }}" alt="Current hearing image" class="max-w-full h-32 object-cover rounded-lg border">
        </div>
    @endif
    
    <!-- File Input with Custom Styling -->
    <div class="relative">
        <input 
            type="file" 
            name="{{ $name }}" 
            id="{{ $name }}"
            accept="image/jpeg,image/jpg,image/webp"
            class="hidden"
            onchange="previewImage(this, '{{ $name }}_preview')"
            {{ $required ? 'required' : '' }}
        >
        
        <!-- Custom Upload Button -->
        <label for="{{ $name }}" class="cursor-pointer inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
            <x-icon name="upload" class="w-5 h-5 mr-2" />
            Choose Image
        </label>
        
        <!-- Selected File Name -->
        <span id="{{ $name }}_filename" class="ml-3 text-sm text-gray-600"></span>
    </div>
    
    <!-- Image Preview -->
    <div id="{{ $name }}_preview_container" class="mt-4 hidden">
        <div class="text-sm text-gray-600 mb-2">Preview:</div>
        <img id="{{ $name }}_preview" alt="Image preview" class="max-w-full h-32 object-cover rounded-lg border">
        
        <!-- Image Dimensions Info -->
        <div id="{{ $name }}_dimensions" class="text-xs text-gray-500 mt-2"></div>
        
        <!-- Remove Button -->
        <button type="button" onclick="removeImage('{{ $name }}')" class="mt-2 text-sm text-red-600 hover:text-red-800">
            Remove image
        </button>
    </div>
    
    <!-- Help Text -->
    @if($helpText)
        <p class="text-sm text-gray-500 mt-2">{{ $helpText }}</p>
    @endif
    
    <!-- Error Message -->
    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<script>
function previewImage(input, previewId) {
    const file = input.files[0];
    const filenameSpan = document.getElementById(input.name + '_filename');
    const previewContainer = document.getElementById(input.name + '_preview_container');
    const previewImg = document.getElementById(previewId);
    const dimensionsDiv = document.getElementById(input.name + '_dimensions');
    
    console.log('File selected:', file); // Debug log
    
    if (file) {
        // Show filename
        filenameSpan.textContent = file.name;
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('hidden');
            
            // Get image dimensions
            previewImg.onload = function() {
                const width = this.naturalWidth;
                const height = this.naturalHeight;
                const ratio = width / height;
                
                console.log('Image dimensions:', width, 'x', height, 'ratio:', ratio); // Debug log
                
                let dimensionText = `${width} × ${height}px`;
                
                // Provide feedback on aspect ratio
                if (ratio >= 2.5 && ratio <= 3.5) {
                    dimensionText += ' ✓ Great landscape ratio for headers';
                } else if (ratio >= 1.5) {
                    dimensionText += ' ✓ Good landscape ratio';
                } else if (ratio >= 0.9 && ratio <= 1.1) {
                    dimensionText += ' ⚠ Square format - consider a landscape image for headers';
                } else {
                    dimensionText += ' ⚠ Portrait format - consider a landscape image for headers';
                }
                
                dimensionsDiv.textContent = dimensionText;
            };
        };
        reader.readAsDataURL(file);
    } else {
        // Clear preview
        filenameSpan.textContent = '';
        previewContainer.classList.add('hidden');
    }
}

function removeImage(inputName) {
    const input = document.getElementById(inputName);
    const filenameSpan = document.getElementById(inputName + '_filename');
    const previewContainer = document.getElementById(inputName + '_preview_container');
    
    input.value = '';
    filenameSpan.textContent = '';
    previewContainer.classList.add('hidden');
}
</script>
