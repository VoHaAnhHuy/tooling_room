<?php

namespace App\Http\Controllers;

use App\Http\Resources\CabinetResource;
use App\Models\Cabinet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CabinetController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $paginator = Cabinet::paginate($perPage);
        return $this->paginated($paginator, CabinetResource::collection($paginator));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cabinet_id'    => 'required|string|max:50|unique:cabinets,cabinet_id',
            'cabinet_name'  => 'required|string|max:100',
            'total_rows'    => 'required|integer|min:1',
            'total_columns' => 'required|integer|min:1',
        ]);
        $cabinet = Cabinet::create($validated);
        return $this->created(new CabinetResource($cabinet));
    }

    public function show(string $id): JsonResponse
    {
        $cabinet = Cabinet::with('slots')->find($id);
        if (!$cabinet) return $this->notFound('Cabinet not found');
        return $this->success(new CabinetResource($cabinet));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $cabinet = Cabinet::find($id);
        if (!$cabinet) return $this->notFound('Cabinet not found');
        $validated = $request->validate([
            'cabinet_name'  => 'sometimes|string|max:100',
            'total_rows'    => 'sometimes|integer|min:1',
            'total_columns' => 'sometimes|integer|min:1',
        ]);
        $cabinet->update($validated);
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
