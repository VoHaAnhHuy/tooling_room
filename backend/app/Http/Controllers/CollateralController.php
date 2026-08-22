<?php

namespace App\Http\Controllers;

use App\Http\Requests\Collateral\AssignSlotRequest;
use App\Http\Requests\Collateral\StoreCollateralRequest;
use App\Http\Requests\Collateral\UpdateCollateralRequest;
use App\Http\Resources\CollateralResource;
use App\Models\Collateral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollateralController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $query   = Collateral::with(['type', 'currentSlot.cabinet']);

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

    public function store(StoreCollateralRequest $request): JsonResponse
    {
        $collateral = Collateral::create($request->validated());
        $collateral->load(['type', 'currentSlot']);
        return $this->created(new CollateralResource($collateral));
    }

    public function show(string $id): JsonResponse
    {
        $collateral = Collateral::with(['type', 'currentSlot.cabinet', 'products'])->find($id);
        if (!$collateral) return $this->notFound('Collateral not found');
        return $this->success(new CollateralResource($collateral));
    }

    public function update(UpdateCollateralRequest $request, string $id): JsonResponse
    {
        $collateral = Collateral::find($id);
        if (!$collateral) return $this->notFound('Collateral not found');
        $collateral->update($request->validated());
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

    public function assignSlot(AssignSlotRequest $request, string $id): JsonResponse
    {
        $collateral = Collateral::find($id);
        if (!$collateral) return $this->notFound('Collateral not found');

        $collateral->update([
            'current_slot_id' => $request->validated('slot_id'),
            'location_status' => 'in_cabinet',
        ]);
        $collateral->load(['currentSlot.cabinet', 'type']);
        return $this->success(new CollateralResource($collateral), 'Collateral assigned to slot');
    }
}
