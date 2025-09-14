<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Thank You for Signing Up!</h2>
        <p class="mb-4">Your account has been created successfully.</p>
        <p class="mb-4">We've sent you an email verification link. Please check your inbox and click the verification link to complete your registration and start receiving housing alerts.</p>
        
        <div class="mt-6">
            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Return to Login
            </a>
        </div>
    </div>
</x-guest-layout>
