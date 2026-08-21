<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $paginator = Product::paginate($perPage);
        return $this->paginated($paginator, ProductResource::collection($paginator));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id'   => 'required|string|max:50|unique:products,product_id',
            'product_name' => 'required|string|max:100',
        ]);
        $product = Product::create($validated);
        return $this->created(new ProductResource($product));
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::with('collaterals')->find($id);
        if (!$product) return $this->notFound('Product not found');
        return $this->success(new ProductResource($product));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) return $this->notFound('Product not found');
        $validated = $request->validate([
            'product_name' => 'required|string|max:100',
        ]);
        $product->update($validated);
        return $this->success(new ProductResource($product));
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) return $this->notFound('Product not found');
        $product->delete();
        return $this->noContent('Product deleted');
    }

    public function collaterals(string $id): JsonResponse
    {
        $product = Product::with('collaterals.type')->find($id);
        if (!$product) return $this->notFound('Product not found');

        // Dùng CollateralResource cho danh sách collaterals
        return $this->success(\App\Http\Resources\CollateralResource::collection($product->collaterals));
    }
}
