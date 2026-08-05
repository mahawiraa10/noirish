<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\ContactMessage;
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
        // 1. LOGIKA SETTINGS - Wrap total
        if (!app()->runningInConsole()) {
            try {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::pluck('value', 'key')->all();
                    View::share('settings', $settings);
                }
            } catch (\Exception $e) {}
        }

        // 2. LOGIKA FORCE URL
        $appUrl = config('app.url');
        if ($appUrl && !in_array($appUrl, ['http://localhost', 'http://noirish.test'])) {
            URL::forceRootUrl($appUrl);
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        // 3. LOGIKA NOTIFIKASI - Skip total kalau lagi build/console
        if (!app()->runningInConsole()) {
            // Admin
            View::composer('layouts.admin', function ($view) {
                try {
                    if (Schema::hasTable('contact_messages')) {
                        $count = ContactMessage::where('is_admin_reply', false)->where('is_read', false)->count();
                        $view->with('adminUnreadCount', $count);
                    }
                } catch (\Exception $e) {}
            });

            // Customer
            View::composer('*', function ($view) {
                try {
                    if (Auth::check() && Auth::user()->role === 'user' && Schema::hasTable('contact_messages')) {
                        $count = ContactMessage::where('user_id', Auth::id())->where('is_admin_reply', true)->where('is_read', false)->count();
                        $view->with('customerUnreadCount', $count);
                    } else {
                        $view->with('customerUnreadCount', 0);
                    }
                } catch (\Exception $e) {
                    $view->with('customerUnreadCount', 0);
                }
            });
        }
    }
}