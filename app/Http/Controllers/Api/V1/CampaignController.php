<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    // Campaign model in ADA Co-OS is likely missing or using a different name if not present.
    // Assuming Campaign model exists with basic CRUD.
    public function index(Request $request): JsonResponse
    {
        if (!class_exists(\App\Models\Campaign::class)) {
            return response()->json(['message' => 'Campaign module not enabled'], 501);
        }

        $campaigns = \App\Models\Campaign::where('tenant_id', $request->user()->tenant_id)
            ->paginate($request->integer('per_page', 20));

        return response()->json($campaigns);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        if (!class_exists(\App\Models\Campaign::class)) {
            return response()->json(['message' => 'Campaign module not enabled'], 501);
        }

        $campaign = \App\Models\Campaign::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        return response()->json($campaign);
    }
}
