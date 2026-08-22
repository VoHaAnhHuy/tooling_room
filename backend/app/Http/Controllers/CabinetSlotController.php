<?php

namespace App\Http\Controllers;

use App\Http\Requests\CabinetSlot\StoreCabinetSlotRequest;
use App\Http\Requests\CabinetSlot\UpdateCabinetSlotRequest;
use App\Http\Resources\CabinetSlotResource;
use App\Models\Cabinet;
use App\Models\CabinetSlot;
use Illuminate\Http\JsonResponse;

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

    public function store(StoreCabinetSlotRequest $request, string $cabinetId): JsonResponse
    {
        $cabinet = Cabinet::find($cabinetId);
        if (!$cabinet) return $this->notFound('Cabinet not found');

        $slot = CabinetSlot::create(array_merge($request->validated(), [
            'cabinet_id' => $cabinetId,
        ]));

        return $this->created(new CabinetSlotResource($slot));
    }

    public function show(string $slotId): JsonResponse
    {
        $slot = CabinetSlot::with(['cabinet', 'currentCollateral'])->find($slotId);
        if (!$slot) return $this->notFound('Slot not found');
        return $this->success(new CabinetSlotResource($slot));
    }

    public function update(UpdateCabinetSlotRequest $request, string $slotId): JsonResponse
    {
        $slot = CabinetSlot::find($slotId);
        if (!$slot) return $this->notFound('Slot not found');
        $slot->update($request->validated());
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
