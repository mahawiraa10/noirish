@extends('layouts.admin')

@section('title', 'Website Settings')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-bold mb-6 text-slate-800">Website Settings</h2>

    {{-- Tampilkan pesan sukses kalo ada --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Form buat update settings --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6"> {{-- Jarak antar section ditambah --}}
        @csrf {{-- Jangan lupa CSRF token --}}

        {{-- Section: Store Information --}}
        <div>
            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Store Information</h3>
            <div class="space-y-4">
                {{-- Store Name --}}
                <div>
                    <label for="store_name" class="block text-sm font-medium text-slate-600 mb-1">Store Name</label>
                    <input type="text" id="store_name" name="store_name"
                           value="{{ old('store_name', $settings['store_name'] ?? '') }}"
                           class="w-full px-4 py-2 border border-stone-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stone-300 focus:border-stone-500">
                    @error('store_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Contact Email --}}
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-slate-600 mb-1">Contact Email (for Frontend)</label>
                    <input type="email" id="contact_email" name="contact_email"
                           value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                           class="w-full px-4 py-2 border border-stone-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stone-300 focus:border-stone-500">
                     @error('contact_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Contact Phone --}}
                 <div>
                    <label for="contact_phone" class="block text-sm font-medium text-slate-600 mb-1">Contact Phone (for Frontend)</label>
                    <input type="tel" id="contact_phone" name="contact_phone"
                           value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                           class="w-full px-4 py-2 border border-stone-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stone-300 focus:border-stone-500">
                     @error('contact_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                 {{-- Store Address --}}
                <div>
                    <label for="store_address" class="block text-sm font-medium text-slate-600 mb-1">Store Address (for Frontend)</label>
                    <textarea id="store_address" name="store_address" rows="3"
                              class="w-full px-4 py-2 border border-stone-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stone-300 focus:border-stone-500"
                    >{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
                     @error('store_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Section: Notifications --}}
        <div class="pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Notifications</h3>
             <div class="space-y-4">
                {{-- Notification Email --}}
                <div>
                     <label for="notification_email" class="block text-sm font-medium text-slate-600 mb-1">Notification Email (Receive order alerts)</label>
                    <input type="email" id="notification_email" name="notification_email"
                           value="{{ old('notification_email', $settings['notification_email'] ?? '') }}"
                           class="w-full px-4 py-2 border border-stone-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stone-300 focus:border-stone-500">
                     <p class="text-xs text-gray-500 mt-1">Email address to receive notifications for new orders.</p>
                     @error('notification_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Notification Preferences --}}
                <div>
                     <label class="block text-sm font-medium text-slate-600 mb-2">Notification Preferences</label>
                     <div class="flex items-center">
                        {{-- Checkbox buat notif order baru --}}
                        <input id="notify_on_new_order" name="notify_on_new_order" type="checkbox" value="1"
                               {{-- Cek value lama --}}
                               {{ old('notify_on_new_order', $settings['notify_on_new_order'] ?? '0') == '1' ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-slate-600 focus:ring-slate-500">
                        <label for="notify_on_new_order" class="ml-2 block text-sm text-gray-900">
                            Receive email notification for each new order
                        </label>
                     </div>
                     @error('notify_on_new_order')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                     {{-- Tambahin checkbox lain buat notif lain kalo perlu --}}
                </div>
            </div>
        </div>


        {{-- Tombol Save --}}
        <div class="pt-6 border-t border-gray-200">
            <button type="submit"
                    class="bg-slate-700 text-white px-6 py-2 rounded-lg font-semibold hover:bg-slate-800 transition duration-300 shadow-md focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection