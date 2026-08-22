<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\CollateralResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage   = $request->integer('per_page', 20);
        $paginator = Product::paginate($perPage);
        return $this->paginated($paginator, ProductResource::collection($paginator));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return $this->created(new ProductResource($product));
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::with('collaterals')->find($id);
        if (!$product) return $this->notFound('Product not found');
        return $this->success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) return $this->notFound('Product not found');
        $product->update($request->validated());
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
        return $this->success(CollateralResource::collection($product->collaterals));
    }
}
