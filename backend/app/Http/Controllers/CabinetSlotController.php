<?php

namespace App\Http\Controllers;

use App\Http\Resources\CabinetSlotResource;
use App\Models\Cabinet;
use App\Models\CabinetSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CabinetSlotController extends ApiController
{
    public function index(string $cabinetId): JsonResponse
    {
        $cabinet = Cabinet::find($cabinetId);
        if (!$cabinet) return $this->notFound('Cabinet not found');

        $slots = CabinetSlot::with('currentCollateral')
            ->where('cabinet_id', $cabinetId)
            ->get();

        return $this->success(CabinetSlotResource::collection($slots));
    }

    public function store(Request $request, string $cabinetId): JsonResponse
    {
        $cabinet = Cabinet::find($cabinetId);
        if (!$cabinet) return $this->notFound('Cabinet not found');

        $validated = $request->validate([
            'slot_id'      => 'required|string|max:50|unique:cabinet_slots,slot_id',
            'row_index'    => 'required|integer|min:0',
            'column_index' => 'required|integer|min:0',
        ]);
        $validated['cabinet_id'] = $cabinetId;
        $slot = CabinetSlot::create($validated);
        return $this->created(new CabinetSlotResource($slot));
    }

    public function show(string $slotId): JsonResponse
    {
        $slot = CabinetSlot::with(['cabinet', 'currentCollateral'])->find($slotId);
        if (!$slot) return $this->notFound('Slot not found');
        return $this->success(new CabinetSlotResource($slot));
    }

    public function update(Request $request, string $slotId): JsonResponse
    {
        $slot = CabinetSlot::find($slotId);
        if (!$slot) return $this->notFound('Slot not found');
        $validated = $request->validate([
            'row_index'    => 'sometimes|integer|min:0',
            'column_index' => 'sometimes|integer|min:0',
        ]);
        $slot->update($validated);
        return $this->success(new CabinetSlotResource($slot));
    }

    public function destroy(string $slotId): JsonResponse
    {
        $slot = CabinetSlot::find($slotId);
        if (!$slot) return $this->notFound('Slot not found');
        $slot->delete();
        return $this->noContent('Slot deleted');
    }
}
