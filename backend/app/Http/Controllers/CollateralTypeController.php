<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollateralTypeResource;
use App\Models\CollateralType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollateralTypeController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $paginator = CollateralType::paginate($perPage);
        return $this->paginated($paginator, CollateralTypeResource::collection($paginator));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:100|unique:collateral_types,type_name',
        ]);
        $type = CollateralType::create($validated);
        return $this->created(new CollateralTypeResource($type));
    }

    public function show(int $id): JsonResponse
    {
        $type = CollateralType::with('collaterals')->find($id);
        if (!$type) return $this->notFound('Collateral type not found');
        return $this->success(new CollateralTypeResource($type));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $type = CollateralType::find($id);
        if (!$type) return $this->notFound('Collateral type not found');
        $validated = $request->validate([
            'type_name' => 'required|string|max:100|unique:collateral_types,type_name,' . $id . ',type_id',
        ]);
        $type->update($validated);
        return $this->success(new CollateralTypeResource($type));
    }

    public function destroy(int $id): JsonResponse
    {
        $type = CollateralType::find($id);
        if (!$type) return $this->notFound('Collateral type not found');
        $type->delete();
        return $this->noContent('Collateral type deleted');
    }
}
