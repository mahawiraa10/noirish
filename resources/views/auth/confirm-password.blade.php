<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            {{-- Tombol (DIUBAH) --}}
            <button type="submit" 
                    class="bg-white text-black border border-black px-4 py-2 rounded-md hover:bg-black hover:text-white transition duration-300 text-xs font-bold uppercase tracking-widest">
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</x-guest-layout>