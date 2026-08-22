<?php

namespace App\Services\Planning;

class CriticalPathEngine
{
    /**
     * Calculate Critical Path Method (CPM) metrics on an array of project tasks
     * Each task: ['id' => 1, 'name' => 'Design', 'duration' => 4, 'dependencies' => []]
     */
    public static function calculate(array $tasks): array
    {
        $taskMap = [];
        foreach ($tasks as $t) {
            $taskMap[$t['id']] = [
                'id' => $t['id'],
                'name' => $t['name'] ?? '',
                'duration' => max(1, (int)($t['duration'] ?? 1)),
                'dependencies' => $t['dependencies'] ?? [],
                'early_start' => 0,
                'early_finish' => 0,
                'late_start' => 0,
                'late_finish' => 0,
                'total_float' => 0,
                'is_critical' => false,
            ];
        }

        // 1. Forward Pass (Calculate Early Start & Early Finish)
        $visited = [];
        foreach ($taskMap as $id => &$task) {
            self::forwardPass($id, $taskMap, $visited);
        }
        unset($task);

        // Project duration is max early_finish
        $maxDuration = 0;
        foreach ($taskMap as $t) {
            if ($t['early_finish'] > $maxDuration) {
                $maxDuration = $t['early_finish'];
            }
        }

        // 2. Backward Pass (Calculate Late Start & Late Finish)
        foreach ($taskMap as $id => &$task) {
            $task['late_finish'] = $maxDuration;
            $task['late_start'] = $maxDuration - $task['duration'];
        }
        unset($task);

        // Successors map
        $successors = [];
        foreach ($taskMap as $id => $task) {
            foreach ($task['dependencies'] as $depId) {
                $successors[$depId][] = $id;
            }
        }

        $visitedBack = [];
        foreach (array_reverse(array_keys($taskMap)) as $id) {
            self::backwardPass($id, $taskMap, $successors, $visitedBack);
        }

        // 3. Compute Float and Mark Critical Path (Float == 0)
        $criticalTasks = [];
        foreach ($taskMap as $id => &$task) {
            $task['total_float'] = max(0, $task['late_start'] - $task['early_start']);
            $task['is_critical'] = ($task['total_float'] === 0);
            if ($task['is_critical']) {
                $criticalTasks[] = $id;
            }
        }

        return [
            'project_duration_days' => $maxDuration,
            'critical_path_task_ids' => $criticalTasks,
            'tasks' => array_values($taskMap),
        ];
    }

    protected static function forwardPass($id, array &$taskMap, array &$visited): void
    {
        if (isset($visited[$id])) return;

        $maxPrevFinish = 0;
        foreach ($taskMap[$id]['dependencies'] as $depId) {
            if (isset($taskMap[$depId])) {
                self::forwardPass($depId, $taskMap, $visited);
                $maxPrevFinish = max($maxPrevFinish, $taskMap[$depId]['early_finish']);
            }
        }

        $taskMap[$id]['early_start'] = $maxPrevFinish;
        $taskMap[$id]['early_finish'] = $maxPrevFinish + $taskMap[$id]['duration'];
        $visited[$id] = true;
    }

    protected static function backwardPass($id, array &$taskMap, array $successors, array &$visited): void
    {
        if (isset($visited[$id])) return;

        if (!empty($successors[$id])) {
            $minSuccStart = PHP_INT_MAX;
            foreach ($successors[$id] as $succId) {
                if (isset($taskMap[$succId])) {
                    self::backwardPass($succId, $taskMap, $successors, $visited);
                    $minSuccStart = min($minSuccStart, $taskMap[$succId]['late_start']);
                }
            }
            $taskMap[$id]['late_finish'] = $minSuccStart;
            $taskMap[$id]['late_start'] = $minSuccStart - $taskMap[$id]['duration'];
        }

        $visited[$id] = true;
    }
}
