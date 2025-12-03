<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
// !! TAMBAHAN PENTING YANG KETINGGALAN !!
use Illuminate\Support\Facades\Auth;
use App\Models\ContactMessage;
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
use App\Models\Setting;
use Midtrans\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       // 1. LOGIKA SETTINGS (Biarin aja)
        if (Schema::hasTable('settings')) {
            $settings = Setting::pluck('value', 'key')->all();
            View::share('settings', $settings);
        }

        // 2. LOGIKA FORCE URL (VERSI GALAK)
        // Ambil URL dari .env
        $appUrl = config('app.url');

        // Kalau .env isinya BUKAN localhost atau noirish.test (berarti lagi pake Tunnel/Ngrok)
        if ($appUrl && $appUrl !== 'http://localhost' && $appUrl !== 'http://noirish.test') {
            // Paksa Laravel pake URL itu buat semua link/gambar
            \Illuminate\Support\Facades\URL::forceRootUrl($appUrl);
            
            // Kalau linknya https, paksa https juga
            if (str_starts_with($appUrl, 'https://')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        // ============================================================
        // 3. LOGIKA NOTIFIKASI CHAT
        // ============================================================
        
        // A. Admin (Pesan dari User)
        View::composer('layouts.admin', function ($view) {
            if (Schema::hasTable('contact_messages')) {
                $unreadMsgCount = ContactMessage::where('is_admin_reply', false)
                                                ->where('is_read', false)
                                                ->count();
                $view->with('adminUnreadCount', $unreadMsgCount);
            }
        });

        // B. Customer (Pesan dari Admin)
        View::composer('*', function ($view) {
            $customerUnreadCount = 0;
            
            if (Auth::check() && Auth::user()->role === 'user' && Schema::hasTable('contact_messages')) {
                $customerUnreadCount = ContactMessage::where('user_id', Auth::id())
                                                     ->where('is_admin_reply', true)
                                                     ->where('is_read', false)
                                                     ->count();
            }
            $view->with('customerUnreadCount', $customerUnreadCount);
        });
    }
}