<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email')" class="!text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full custom-input-field" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="!text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full custom-input-field" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="!text-gray-700" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full custom-input-field"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" 
                    class="bg-white text-black border border-black px-4 py-2 rounded-md hover:bg-black hover:text-white transition duration-300 text-xs font-bold uppercase tracking-widest">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</x-guest-layout>