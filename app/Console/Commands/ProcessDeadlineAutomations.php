<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\AutomationEngine;
use Illuminate\Console\Command;

class ProcessDeadlineAutomations extends Command
{
    protected $signature = 'nexus:process-deadlines';
    protected $description = 'Fire deadline_passed automations for overdue tasks';

    public function handle(): int
    {
        $engine = app(AutomationEngine::class);

        $overdueTasks = Task::whereNotNull('due_date')
            ->where('due_date', '<', now()->startOfDay())
            ->where('status', '!=', 'done')
            ->get();

        $count = 0;
        foreach ($overdueTasks as $task) {
            $engine->evaluate('Task', 'deadline_passed', $task);
            $count++;
        }

        $this->info("Processed {$count} overdue tasks.");
        return self::SUCCESS;
    }
}
