<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\AutomationDaemon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
        // ✅ AUTO START: Hanya di development dan kondisi yang tepat
        if (app()->environment(['local', 'development']) && 
            !app()->runningInConsole() &&
            $this->shouldStartAutomation()) {
            
            $this->autoStartDaemon();
        }
    }

    /**
     * ✅ CHECK: Apakah perlu start automation (anti-spam)
     */
    private function shouldStartAutomation()
    {
        // ✅ CACHE: Cek terakhir kali start daemon (cooldown 10 menit)
        $cacheKey = 'automation_start_attempt';
        $lastAttempt = Cache::get($cacheKey);
        
        if ($lastAttempt && now()->diffInMinutes($lastAttempt) < 10) {
            return false; // Masih dalam cooldown
        }
        
        // ✅ CHECK: Daemon masih berjalan
        if (AutomationDaemon::isDaemonRunning()) {
            return false; // Daemon masih aktif
        }
        
        // ✅ SET: Cache untuk cooldown
        Cache::put($cacheKey, now(), 600); // 10 menit cache
        
        return true;
    }

    /**
     * ✅ AUTO START: Daemon saat web server dimulai
     */
    private function autoStartDaemon()
    {
        try {
            // ✅ START: Daemon dengan interval 5 menit
            if (AutomationDaemon::autoStart(300)) {
                Log::info('🚀 Automation daemon auto-started via web request', [
                    'interval' => 300,
                    'started_at' => now()->toDateTimeString(),
                    'user_agent' => request()->userAgent(),
                    'ip' => request()->ip(),
                    'cooldown_minutes' => 10
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to auto-start daemon via AppServiceProvider', [
                'error' => $e->getMessage()
            ]);
        }
    }
}