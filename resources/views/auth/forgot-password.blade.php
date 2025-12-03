<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700" style="color: #374151 !important;" />
            <x-text-input id="email" class="block mt-1 w-full custom-input-field" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            {{-- Tombol (DIUBAH) --}}
            <button type="submit" 
                    class="bg-white text-black border border-black px-4 py-2 rounded-md hover:bg-black hover:text-white transition duration-300 text-xs font-bold uppercase tracking-widest">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>
    </form>
</x-guest-layout>