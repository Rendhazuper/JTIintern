<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimpleAutomationService;
use Carbon\Carbon;

class AutomationDaemon extends Command
{
    protected $signature = 'automation:daemon {--interval=3600}';
    protected $description = 'Run automation daemon that checks periodically';

    private $running = true;

    public function handle()
    {
        $interval = (int) $this->option('interval'); // Default 1 hour
        
        $this->info("🤖 Starting Automation Daemon");
        $this->info("⏰ Check interval: {$interval} seconds");
        $this->info("🔄 Press Ctrl+C to stop");
        $this->newLine();

        // ✅ SIGNAL HANDLING untuk graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'handleSignal']);
            pcntl_signal(SIGINT, [$this, 'handleSignal']);
        }

        $service = new SimpleAutomationService();
        $lastRun = null;

        while ($this->running) {
            try {
                $now = Carbon::now();
                
                $this->line("[{$now->format('Y-m-d H:i:s')}] 🔍 Checking for expired magang...");
                
                // ✅ RUN: Automation
                $result = $service->autoCompleteExpired();
                
                if ($result['success']) {
                    if ($result['completed'] > 0) {
                        $this->info("✅ Completed {$result['completed']} magang");
                    } else {
                        $this->line("ℹ️  No expired magang found");
                    }
                } else {
                    $this->error("❌ Automation failed: " . $result['error']);
                }

                $lastRun = $now;

                // ✅ WAIT: Sleep until next check
                $this->line("💤 Sleeping for {$interval} seconds...");
                $this->newLine();
                
                sleep($interval);

                // ✅ SIGNAL CHECK (untuk Unix systems)
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }

            } catch (\Exception $e) {
                $this->error("💥 Daemon error: " . $e->getMessage());
                $this->line("⏳ Waiting 60 seconds before retry...");
                sleep(60);
            }
        }

        $this->newLine();
        $this->info("🛑 Automation Daemon stopped gracefully");
        $this->info("📊 Last run: " . ($lastRun ? $lastRun->format('Y-m-d H:i:s') : 'Never'));
    }

    /**
     * ✅ HANDLE: Graceful shutdown signals
     */
    public function handleSignal($signal)
    {
        $this->newLine();
        $this->warn("🔔 Received shutdown signal: {$signal}");
        $this->running = false;
    }
}