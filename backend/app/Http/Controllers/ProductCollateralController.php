<?php

namespace App\Http\Controllers;

use App\Models\Collateral;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCollateralController extends ApiController
{
    /**
     * POST /api/products/{productId}/collaterals
     * Gắn một collateral vào product
     */
    public function attach(Request $request, string $productId): JsonResponse
    {
        $product = Product::find($productId);
        if (!$product) return $this->notFound('Product not found');

        $validated = $request->validate([
            'collateral_id' => 'required|string|exists:collaterals,collateral_id',
        ]);

        // Tránh duplicate
        $exists = $product->collaterals()->where('collaterals.collateral_id', $validated['collateral_id'])->exists();
        if ($exists) {
            return $this->error('Collateral already linked to this product', 409);
        }

        $product->collaterals()->attach($validated['collateral_id']);
        return $this->success(null, 'Collateral attached to product');
    }

    /**
     * DELETE /api/products/{productId}/collaterals/{collateralId}
     * Gỡ một collateral khỏi product
     */
    public function detach(string $productId, string $collateralId): JsonResponse
    {
        $product = Product::find($productId);
        if (!$product) return $this->notFound('Product not found');

        $collateral = Collateral::find($collateralId);
        if (!$collateral) return $this->notFound('Collateral not found');

        $product->collaterals()->detach($collateralId);
        return $this->noContent('Collateral detached from product');
    }
}
