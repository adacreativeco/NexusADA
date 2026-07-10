<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRecurringTasks extends Command
{
    protected $signature = 'nexus:process-recurring';
    protected $description = 'Create new tasks from recurring templates';

    public function handle(): int
    {
        $templates = Task::recurringTemplates()->get();
        $created = 0;

        foreach ($templates as $template) {
            // Check end date
            if ($template->recurrence_end_date && Carbon::parse($template->recurrence_end_date)->isPast()) {
                continue;
            }

            $nextDate = $template->nextRecurrenceDate();
            if (!$nextDate || $nextDate->isFuture()) {
                continue;
            }

            // Create the task copy
            $newTask = Task::create([
                'title' => $template->title,
                'description' => $template->description,
                'status' => 'todo',
                'priority' => $template->priority,
                'project_id' => $template->project_id,
                'assigned_to' => $template->assigned_to,
                'due_date' => $template->due_date
                    ? $nextDate->copy()->addDays(
                        Carbon::parse($template->created_at)->diffInDays($template->due_date)
                    )
                    : null,
                'parent_task_id' => $template->id,
                'is_recurring' => false,
            ]);

            $template->update(['last_recurrence_at' => now()]);
            $created++;
        }

        $this->info("Created {$created} recurring tasks.");
        return self::SUCCESS;
    }
}
