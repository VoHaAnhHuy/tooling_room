<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cabinet\StoreCabinetRequest;
use App\Http\Requests\Cabinet\UpdateCabinetRequest;
use App\Http\Resources\CabinetResource;
use App\Models\Cabinet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CabinetController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage   = $request->integer('per_page', 20);
        $paginator = Cabinet::paginate($perPage);
        return $this->paginated($paginator, CabinetResource::collection($paginator));
    }

    public function store(StoreCabinetRequest $request): JsonResponse
    {
        $cabinet = Cabinet::create($request->validated());
        return $this->created(new CabinetResource($cabinet));
    }

    public function show(string $id): JsonResponse
    {
        $cabinet = Cabinet::with('slots')->find($id);
        if (!$cabinet) return $this->notFound('Cabinet not found');
        return $this->success(new CabinetResource($cabinet));
    }

    public function update(UpdateCabinetRequest $request, string $id): JsonResponse
    {
        $cabinet = Cabinet::find($id);
        if (!$cabinet) return $this->notFound('Cabinet not found');
        $cabinet->update($request->validated());
        return $this->success(new CabinetResource($cabinet));
    }

    public function destroy(string $id): JsonResponse
    {
        $cabinet = Cabinet::find($id);
        if (!$cabinet) return $this->notFound('Cabinet not found');
        $cabinet->delete();
        return $this->noContent('Cabinet deleted');
    }
}
