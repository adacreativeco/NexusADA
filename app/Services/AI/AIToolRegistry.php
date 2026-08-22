<?php

namespace App\Services\AI;

use App\Models\Task;
use App\Models\Proposal;
use App\Models\Client;
use App\Models\Project;

class AIToolRegistry
{
    public static function getToolDefinitions(): array
    {
        return [
            [
                'name' => 'create_task',
                'description' => 'Create a new project task in the system',
                'parameters' => ['tenant_id', 'title', 'description', 'due_date'],
            ],
            [
                'name' => 'create_proposal',
                'description' => 'Draft a financial proposal for a client',
                'parameters' => ['tenant_id', 'client_id', 'title', 'total_amount'],
            ],
            [
                'name' => 'lookup_client',
                'description' => 'Retrieve client profile and project history',
                'parameters' => ['tenant_id', 'client_id'],
            ],
        ];
    }

    public static function executeTool(string $toolName, array $params): array
    {
        return match ($toolName) {
            'create_task' => [
                'status' => 'success',
                'task' => Task::create([
                    'tenant_id' => $params['tenant_id'],
                    'title' => $params['title'],
                    'description' => $params['description'] ?? '',
                    'due_date' => $params['due_date'] ?? now()->addDays(3),
                    'status' => 'todo',
                ]),
            ],
            'create_proposal' => [
                'status' => 'success',
                'proposal' => Proposal::create([
                    'tenant_id' => $params['tenant_id'],
                    'client_id' => $params['client_id'],
                    'title' => $params['title'],
                    'total_amount' => $params['total_amount'] ?? 0,
                    'status' => 'draft',
                ]),
            ],
            'lookup_client' => [
                'status' => 'success',
                'client' => Client::with('projects')->find($params['client_id']),
            ],
            default => ['status' => 'error', 'message' => "Unknown tool: {$toolName}"],
        };
    }
}
