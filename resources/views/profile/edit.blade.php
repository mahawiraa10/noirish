@extends('layouts.settings')

@section('title', 'My Profile')

@section('content')
    {{-- Layout 2 Kolom --}}
    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        
        {{-- 1. Sidebar Navigasi --}}
        <aside class="lg:col-span-3 mb-8 lg:mb-0">
            <nav class="space-y-1">
                {{-- Link Aktif: bg-slate-100 text-slate-800 --}}
                <a href="{{ route('profile.edit') }}" 
                   class="bg-slate-100 text-slate-800 group flex items-center px-3 py-2 text-sm font-bold rounded-lg transition-colors"
                   aria-current="page">
                    <svg class="text-slate-600 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.875 1.875 0 0 1 18 22.5H6a1.875 1.875 0 0 1-1.501-2.382Z" />
                    </svg>
                    <span class="truncate">Personal Information</span>
                </a>

                {{-- Link Inaktif --}}
                <a href="{{ route('profile.security') }}"
                   class="text-gray-600 hover:bg-slate-50 hover:text-slate-800 group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                    <svg class="text-gray-400 group-hover:text-slate-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
            
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                {{-- Card --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                    
                    <div class="bg-white py-6 px-4 sm:p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold leading-6 text-gray-800">Personal Information</h2>
                        <p class="mt-1 text-sm text-gray-500">Update your account's profile information and email address.</p>
                    </div>

                    <div class="bg-white py-6 px-4 sm:p-6">
                        
                        @if (session('warning'))
                            <div class="p-4 mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg shadow-sm" role="alert">
                                <p class="font-bold">Action Required</p>
                                <p>{{ session('warning') }}</p>
                            </div>
                        @endif

                        @if (session('status') === 'profile-updated')
                            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                                <span class="block sm:inline font-medium">Profile updated successfully.</span>
                            </div>
                        @endif
                    
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            
                            {{-- Full Name --}}
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700">Full Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" 
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('name') border-red-500 @enderror" required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" 
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('email') border-red-500 @enderror" required>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700">Phone Number</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->profile->phone) }}" placeholder="+62 812 3456 7890"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date of Birth --}}
                            <div>
                                <label for="birth_date" class="block text-sm font-semibold text-gray-700">Date of Birth</label>
                                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $customer->profile->birth_date) }}" 
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('birth_date') border-red-500 @enderror">
                                @error('birth_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Gender --}}
                            <div>
                                <label for="gender" class="block text-sm font-semibold text-gray-700">Gender</label>
                                <select name="gender" id="gender" 
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('gender') border-red-500 @enderror">
                                    <option value="" disabled {{ old('gender', $customer->profile->gender) ? '' : 'selected' }}>Select Gender</option>
                                    <option value="Male" {{ old('gender', $customer->profile->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $customer->profile->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- City --}}
                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-700">City</label>
                                <input type="text" name="city" id="city" value="{{ old('city', $customer->profile->city) }}" placeholder="e.g. Jakarta"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('city') border-red-500 @enderror">
                                @error('city')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-semibold text-gray-700">Address</label>
                                <textarea name="address" id="address" rows="4" placeholder="Your full street address"
                                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm transition-colors @error('address') border-red-500 @enderror">{{ old('address', $customer->profile?->address) }}</textarea>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Footer: Tombol Save Changes --}}
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end border-t border-gray-200">
                        {{-- TOMBOL DIUBAH DI SINI --}}
                        <button type="submit" 
                                class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
                            Save Changes
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
@endsection