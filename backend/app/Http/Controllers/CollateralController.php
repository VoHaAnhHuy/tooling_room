<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollateralResource;
use App\Models\Collateral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollateralController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);

        $query = Collateral::with(['type', 'currentSlot.cabinet']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }
        if ($request->filled('location_status')) {
            $query->where('location_status', $request->location_status);
        }

        $paginator = $query->paginate($perPage);
        return $this->paginated($paginator, CollateralResource::collection($paginator));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'collateral_id'   => 'required|string|max:50|unique:collaterals,collateral_id',
            'type_id'         => 'required|integer|exists:collateral_types,type_id',
            'name'            => 'required|string|max:150',
            'location_status' => 'nullable|string|max:20',
            'current_slot_id' => 'nullable|string|max:50|exists:cabinet_slots,slot_id',
            'status'          => 'nullable|string|max:20',
        ]);
        $collateral = Collateral::create($validated);
        $collateral->load(['type', 'currentSlot']);
        return $this->created(new CollateralResource($collateral));
    }

    public function show(string $id): JsonResponse
    {
        $collateral = Collateral::with(['type', 'currentSlot.cabinet', 'products'])->find($id);
        if (!$collateral) return $this->notFound('Collateral not found');
        return $this->success(new CollateralResource($collateral));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $collateral = Collateral::find($id);
        if (!$collateral) return $this->notFound('Collateral not found');
        $validated = $request->validate([
            'type_id'         => 'sometimes|integer|exists:collateral_types,type_id',
            'name'            => 'sometimes|string|max:150',
            'location_status' => 'nullable|string|max:20',
            'current_slot_id' => 'nullable|string|max:50|exists:cabinet_slots,slot_id',
            'status'          => 'nullable|string|max:20',
        ]);
        $collateral->update($validated);
        $collateral->load(['type', 'currentSlot']);
        return $this->success(new CollateralResource($collateral));
    }

    public function destroy(string $id): JsonResponse
    {
        $collateral = Collateral::find($id);
        if (!$collateral) return $this->notFound('Collateral not found');
        $collateral->delete();
        return $this->noContent('Collateral deleted');
    }

    public function assignSlot(Request $request, string $id): JsonResponse
    {
        $collateral = Collateral::find($id);
        if (!$collateral) return $this->notFound('Collateral not found');

        $validated = $request->validate([
            'slot_id' => 'required|string|exists:cabinet_slots,slot_id',
        ]);
        $collateral->update([
            'current_slot_id' => $validated['slot_id'],
            'location_status' => 'in_cabinet',
        ]);
        $collateral->load(['currentSlot.cabinet', 'type']);
        return $this->success(new CollateralResource($collateral), 'Collateral assigned to slot');
    }
}
