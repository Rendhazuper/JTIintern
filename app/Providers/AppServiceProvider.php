<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\AutomationDaemon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        // ✅ FIX: Support both local AND production
        if (!app()->runningInConsole()) {
            $this->startAutomationByEnvironment();
        }
    }

    private function startAutomationByEnvironment()
    {
        try {
            $environment = config('app.env');
            $isDebugMode = $environment === 'local';
            
            Log::info('🔧 AppServiceProvider checking environment', [
                'environment' => $environment,
                'is_debug_mode' => $isDebugMode,
                'console_check' => app()->runningInConsole()
            ]);
            
            // ✅ PRODUCTION: Business hours check
            if (!$isDebugMode && $this->isBusinessHours()) {
                Log::info('🚫 Skipping automation - business hours active');
                return;
            }
            
            // ✅ CHECK: Daemon already running
            if (AutomationDaemon::isDaemonRunning()) {
                Log::debug('✅ Automation daemon already running - skipping start');
                return;
            }
            
            // ✅ RATE LIMITING: Environment-based
            $rateLimitMinutes = $isDebugMode ? 1 : 30; // Debug: 1 min, Production: 30 min
            $lastAttempt = Cache::get('automation_start_attempt');
            
            if ($lastAttempt && now()->diffInMinutes(Carbon::parse($lastAttempt)) < $rateLimitMinutes) {
                Log::debug('⏳ Automation start rate limited', [
                    'minutes_since_last' => now()->diffInMinutes(Carbon::parse($lastAttempt)),
                    'rate_limit' => $rateLimitMinutes
                ]);
                return;
            }
            
            Log::info('🚀 Starting automation daemon for environment: ' . $environment);
            
            // ✅ INTERVAL: Environment-based
            $interval = $isDebugMode ? 120 : 3600; // Debug: 2 min, Production: 1 hour
            
            // ✅ START: Daemon
            $result = AutomationDaemon::autoStart($interval);
            
            if ($result) {
                // ✅ CACHE: Environment-based rate limit
                Cache::put('automation_start_attempt', now(), $rateLimitMinutes * 60);
                
                Log::info('✅ Automation daemon started successfully', [
                    'environment' => $environment,
                    'interval' => $interval,
                    'rate_limit_minutes' => $rateLimitMinutes
                ]);
            } else {
                Log::error('❌ Failed to start automation daemon', [
                    'environment' => $environment
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('💥 Error starting automation: ' . $e->getMessage());
        }
    }

    /**
     * ✅ PRODUCTION: Business hours check
     */
    private function isBusinessHours(): bool
    {
        $now = Carbon::now();
        $hour = $now->hour;
        $isWeekday = $now->isWeekday();
        
        // Business hours: 8AM - 5PM on weekdays
        return $isWeekday && $hour >= 8 && $hour < 17;
    }
}