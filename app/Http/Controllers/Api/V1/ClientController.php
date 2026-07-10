<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $clients = $request->user()->tenant->clients()
            ->select('id', 'name', 'email', 'phone', 'status', 'created_at')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json($clients);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $client = $request->user()->tenant->clients()->findOrFail($id);
        return response()->json($client);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'status'  => 'nullable|string|in:active,inactive,lead',
        ]);

        $validated['tenant_id'] = $request->user()->tenant_id;
        $client = Client::create($validated);

        return response()->json($client, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $client = $request->user()->tenant->clients()->findOrFail($id);

        $validated = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'status'  => 'nullable|string|in:active,inactive,lead',
        ]);

        $client->update($validated);

        return response()->json($client);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $client = $request->user()->tenant->clients()->findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client deleted.'], 200);
    }
}
