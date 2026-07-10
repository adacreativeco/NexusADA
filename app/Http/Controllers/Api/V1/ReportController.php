<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Simple mock report for API demonstration
        $tenantId = $request->user()->tenant_id;

        $report = [
            'projects_count' => \App\Models\Project::where('tenant_id', $tenantId)->count(),
            'tasks_count'    => \App\Models\Task::where('tenant_id', $tenantId)->count(),
            'clients_count'  => \App\Models\Client::where('tenant_id', $tenantId)->count(),
            'generated_at'   => now()->toIso8601String()
        ];

        return response()->json($report);
    }
}
