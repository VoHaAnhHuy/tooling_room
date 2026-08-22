<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollateralType\StoreCollateralTypeRequest;
use App\Http\Requests\CollateralType\UpdateCollateralTypeRequest;
use App\Http\Resources\CollateralTypeResource;
use App\Models\CollateralType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollateralTypeController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage   = $request->integer('per_page', 20);
        $paginator = CollateralType::paginate($perPage);
        return $this->paginated($paginator, CollateralTypeResource::collection($paginator));
    }

    public function store(StoreCollateralTypeRequest $request): JsonResponse
    {
        $type = CollateralType::create($request->validated());
        return $this->created(new CollateralTypeResource($type));
    }

    public function show(int $id): JsonResponse
    {
        $type = CollateralType::with('collaterals')->find($id);
        if (!$type) return $this->notFound('Collateral type not found');
        return $this->success(new CollateralTypeResource($type));
    }

    public function update(UpdateCollateralTypeRequest $request, int $id): JsonResponse
    {
        $type = CollateralType::find($id);
        if (!$type) return $this->notFound('Collateral type not found');
        $type->update($request->validated());
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
