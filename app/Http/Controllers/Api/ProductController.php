<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    public function index(Request $request)
    {
        return response()->json(
            Product::query()
                ->with([
                    'category',
                    'subCategory',
                    'brand',
                    'images'
                ])
                ->when(
                    $request->category_id,
                    fn ($q, $v) => $q->where('category_id', $v)
                )
                ->when(
                    $request->brand_id,
                    fn ($q, $v) => $q->where('brand_id', $v)
                )
                ->when(
                    $request->status,
                    fn ($q, $v) => $q->where('status', $v)
                )
                ->when(
                    $request->search,
                    fn ($q, $v) => $q->where('name', 'like', "%{$v}%")
                )
                ->paginate(20)
        );
    }

    // POST /api/products
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product->load([
                'category',
                'subCategory',
                'brand',
                'images'
            ]),
        ], 201);
    }

    // GET /api/products/{product}
    public function show(Product $product)
    {
        return response()->json(
            $product->load([
                'category',
                'subCategory',
                'brand',
                'images',
                'reviews'
            ])
        );
    }

    // PUT/PATCH /api/products/{product}
    public function update(
        UpdateProductRequest $request,
        Product $product
    ) {
        $product->update($request->validated());

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $product->fresh()->load([
                'category',
                'subCategory',
                'brand',
                'images'
            ]),
        ]);
    }

    // DELETE /api/products/{product}
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }
}