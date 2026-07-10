<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * REST API v1 — Projects CRUD
 *
 * All endpoints require Sanctum token authentication.
 * Data is automatically scoped to the authenticated user's tenant.
 */
class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $projects = $request->user()->tenant->projects()
            ->select('id', 'title', 'description', 'status', 'start_date', 'due_date', 'client_id', 'created_at')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json($projects);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $project = $request->user()->tenant->projects()->findOrFail($id);
        $project->load('tasks:id,project_id,title,status,due_date,assigned_to');

        return response()->json($project);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:active,completed,on_hold,cancelled',
            'start_date'  => 'nullable|date',
            'due_date'    => 'nullable|date|after_or_equal:start_date',
            'client_id'   => 'nullable|integer',
        ]);

        $validated['tenant_id'] = $request->user()->tenant_id;
        $project = \App\Models\Project::create($validated);

        return response()->json($project, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $project = $request->user()->tenant->projects()->findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:active,completed,on_hold,cancelled',
            'start_date'  => 'nullable|date',
            'due_date'    => 'nullable|date',
            'client_id'   => 'nullable|integer',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $project = $request->user()->tenant->projects()->findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project deleted.'], 200);
    }
}
