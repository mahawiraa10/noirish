@extends('layouts.settings')

@section('title', 'Security Settings')

@section('content')
    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        
        {{-- 1. Sidebar Navigasi --}}
        <aside class="lg:col-span-3 mb-8 lg:mb-0">
            <nav class="space-y-1">
                <a href="{{ route('profile.edit') }}" 
                   class="text-gray-600 hover:bg-slate-50 hover:text-slate-800 group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                    <svg class="text-gray-400 group-hover:text-slate-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.875 1.875 0 0 1 18 22.5H6a1.875 1.875 0 0 1-1.501-2.382Z" />
                    </svg>
                    <span class="truncate">Personal Information</span>
                </a>

                <a href="{{ route('profile.security') }}"
                   class="bg-slate-100 text-slate-800 group flex items-center px-3 py-2 text-sm font-bold rounded-lg transition-colors"
                   aria-current="page">
                    <svg class="text-slate-600 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 0 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <span class="truncate">Security</span>
                </a>

                 <a href="{{ route('wishlist') }}"
                   class="text-gray-600 hover:bg-slate-50 hover:text-slate-800 group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                    <svg class="text-gray-400 group-hover:text-slate-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                    <span class="truncate">My Wishlist</span>
                </a>

                 <a href="{{ route('profile.orders') }}"
                   class="text-gray-600 hover:bg-slate-50 hover:text-slate-800 group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                    <svg class="text-gray-400 group-hover:text-slate-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="truncate">Order History</span>
                </a>
            </nav>
        </aside>

        {{-- 2. Konten Form --}}
        <div class="lg:col-span-9">
            
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                {{-- Card --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                    
                    <div class="bg-white py-6 px-4 sm:p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold leading-6 text-gray-800">Update Password</h2>
                        <p class="mt-1 text-sm text-gray-500">Ensure your account is using a long, random password to stay secure.</p>
                    </div>

                    <div class="bg-white py-6 px-4 sm:p-6">
                        
                        @if (session('status') === 'password-updated')
                            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                                <span class="block sm:inline font-medium">Password updated successfully.</span>
                            </div>
                        @endif
                    
                        <div class="space-y-5">
                            
                            {{-- Current Password --}}
                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-gray-700">Current Password</label>
                                <input type="password" name="current_password" id="current_password" 
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('current_password', 'updatePassword') border-red-500 @enderror" required>
                                @error('current_password', 'updatePassword')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- New Password --}}
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700">New Password</label>
                                <input type="password" name="password" id="password" 
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('password', 'updatePassword') border-red-500 @enderror" required>
                                @error('password', 'updatePassword')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors" required>
                            </div>
                        </div>
                    </div>

                    {{-- Footer: Tombol Save --}}
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end border-t border-gray-200">
                        {{-- TOMBOL DIUBAH DI SINI --}}
                        <button type="submit" 
                                class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
                            Save
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
@endsection