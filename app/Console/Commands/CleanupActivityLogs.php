<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:activity-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus activity log yang lebih lama dari 3 hari';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = ActivityLog::cleanupOlderThan(3);
        $this->info("{$deleted} activity log lebih lama dari 3 hari telah dihapus.");

        return Command::SUCCESS;
    }
}
