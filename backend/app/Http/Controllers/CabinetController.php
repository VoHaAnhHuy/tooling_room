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

        $slots = [];
        for ($r = 0; $r < $cabinet->total_rows; $r++) {
            for ($c = 0; $c < $cabinet->total_columns; $c++) {
                $slots[] = [
                    'slot_id'      => $cabinet->cabinet_id . '-R' . $r . '-C' . $c,
                    'cabinet_id'   => $cabinet->cabinet_id,
                    'row_index'    => $r,
                    'column_index' => $c,
                ];
            }
        }
        if (!empty($slots)) {
            \App\Models\CabinetSlot::insert($slots);
        }

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

        $oldRows = $cabinet->total_rows;
        $oldCols = $cabinet->total_columns;

        $cabinet->update($request->validated());

        // Nếu tăng kích thước, tạo thêm các slot bị thiếu
        if ($cabinet->total_rows > $oldRows || $cabinet->total_columns > $oldCols) {
            $slots = [];
            for ($r = 0; $r < $cabinet->total_rows; $r++) {
                for ($c = 0; $c < $cabinet->total_columns; $c++) {
                    if ($r >= $oldRows || $c >= $oldCols) {
                        $slots[] = [
                            'slot_id'      => $cabinet->cabinet_id . '-R' . $r . '-C' . $c,
                            'cabinet_id'   => $cabinet->cabinet_id,
                            'row_index'    => $r,
                            'column_index' => $c,
                        ];
                    }
                }
            }
            if (!empty($slots)) {
                // Insert ignore to avoid duplicates if someone already added it
                \App\Models\CabinetSlot::insertOrIgnore($slots);
            }
        }

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
