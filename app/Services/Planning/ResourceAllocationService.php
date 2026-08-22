<?php

namespace App\Services\Planning;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class ResourceAllocationService
{
    /**
     * Compute daily workload hours per team member within a date window
     */
    public static function getTeamCapacityUtilization(int $tenantId, string $startDate, string $endDate, float $dailyLimitHours = 8.0): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $users = User::where('tenant_id', $tenantId)->get();
        $tasks = Task::where('tenant_id', $tenantId)
            ->whereNotNull('assigned_to')
            ->where('status', '!=', 'done')
            ->get();

        $report = [];

        foreach ($users as $user) {
            $userTasks = $tasks->where('assigned_to', $user->id);
            $totalEstimatedHours = $userTasks->sum('estimated_hours') ?: ($userTasks->count() * 4.0);
            $daysCount = max(1, $start->diffInDays($end));
            $averageDailyHours = round($totalEstimatedHours / $daysCount, 1);

            $isOverAllocated = $averageDailyHours > $dailyLimitHours;

            $report[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'active_tasks_count' => $userTasks->count(),
                'total_estimated_hours' => $totalEstimatedHours,
                'average_daily_hours' => $averageDailyHours,
                'is_over_allocated' => $isOverAllocated,
                'utilization_rate_pct' => round(($averageDailyHours / $dailyLimitHours) * 100, 1),
            ];
        }

        return $report;
    }
}
