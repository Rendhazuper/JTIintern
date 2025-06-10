<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimpleAutomationService;

class RunAutomation extends Command
{
    protected $signature = 'automation:run 
                            {--type=completion : Type of automation (completion|warning)}
                            {--days=3 : Days before expiry for warnings}
                            {--dry-run : Show what would happen without executing}';

    protected $description = 'Run simple automation tasks';

    public function handle(SimpleAutomationService $automationService)
    {
        $type = $this->option('type');
        $isDryRun = $this->option('dry-run');

        $this->info("🤖 Running automation: {$type}");
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN MODE - No changes will be made");
        }
        $this->newLine();

        if ($type === 'completion') {
            return $this->runCompletion($automationService, $isDryRun);
        } elseif ($type === 'warning') {
            return $this->runWarnings($automationService, $isDryRun);
        } else {
            $this->error("Invalid type. Use 'completion' or 'warning'");
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }
    }

    private function runCompletion($service, $isDryRun)
    {
        if ($isDryRun) {
            $status = $service->getCurrentStatus();
            $this->info("Would complete {$status['expired_magang']} expired magang");
            return \Symfony\Component\Console\Command\Command::SUCCESS;
        }

        $result = $service->autoCompleteExpired();

        if ($result['success']) {
            $this->info("✅ Completion finished!");
            $this->table(['Metric', 'Count'], [
                ['Total Checked', $result['total_checked']],
                ['Completed', $result['completed']],
                ['Failed', $result['failed']]
            ]);

            // ✅ CACHE: Last run
            cache()->put('last_auto_completion', now(), 86400);
        } else {
            $this->error("❌ Completion failed: " . $result['error']);
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }

    private function runWarnings($service, $isDryRun)
    {
        $days = $this->option('days');

        if ($isDryRun) {
            $this->info("Would check for magang expiring in {$days} days");
            return \Symfony\Component\Console\Command\Command::SUCCESS;
        }

        $result = $service->checkExpiringMagang($days);

        if ($result['success']) {
            $this->info("✅ Warning check finished!");
            $this->info("📧 Sent {$result['notifications_sent']} warnings for {$result['expiring_count']} expiring magang");
        } else {
            $this->error("❌ Warning check failed: " . $result['error']);
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}