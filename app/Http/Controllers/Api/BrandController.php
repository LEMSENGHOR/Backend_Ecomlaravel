<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;

class BrandController extends Controller
{
    // GET /api/brands
    public function index()
    {
        return response()->json(
            Brand::query()->paginate(20)
        );
    }

    // POST /api/brands
    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create($request->validated());

        return response()->json([
            'message' => 'Brand created successfully.',
            'brand' => $brand,
        ], 201);
    }

    // GET /api/brands/{brand}
    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    // PUT/PATCH /api/brands/{brand}
    public function update(
        UpdateBrandRequest $request,
        Brand $brand
    ) {
        $brand->update($request->validated());

        return response()->json([
            'message' => 'Brand updated successfully.',
            'brand' => $brand->fresh(),
        ]);
    }

    // DELETE /api/brands/{brand}
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return response()->json([
            'message' => 'Brand deleted successfully.',
        ]);
    }
}