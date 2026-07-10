<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tasks = $request->user()->tenant->tasks()
            ->select('id', 'title', 'status', 'priority', 'due_date', 'project_id', 'assigned_to', 'created_at')
            ->when($request->project_id, fn($q, $p) => $q->where('project_id', $p))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->assigned_to, fn($q, $a) => $q->where('assigned_to', $a))
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json($tasks);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $task = $request->user()->tenant->tasks()->findOrFail($id);
        return response()->json($task);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:todo,in_progress,review,done',
            'priority'    => 'nullable|string|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'project_id'  => 'nullable|integer',
            'assigned_to' => 'nullable|integer',
        ]);

        $validated['tenant_id'] = $request->user()->tenant_id;
        $task = \App\Models\Task::create($validated);

        return response()->json($task, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $task = $request->user()->tenant->tasks()->findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:todo,in_progress,review,done',
            'priority'    => 'nullable|string|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'assigned_to' => 'nullable|integer',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $task = $request->user()->tenant->tasks()->findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted.'], 200);
    }
}
