<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    // GET /api/sub-categories
    public function index()
    {
        return response()->json(
            SubCategory::query()
                ->with('category')
                ->paginate(20)
        );
    }

    // POST /api/sub-categories
    public function store(StoreSubCategoryRequest $request)
    {
        $subCategory = SubCategory::create($request->validated());

        return response()->json([
            'message' => 'Sub-category created successfully.',
            'sub_category' => $subCategory->load('category'),
        ], 201);
    }

    // GET /api/sub-categories/{sub_category}
    public function show(SubCategory $subCategory)
    {
        return response()->json(
            $subCategory->load('category')
        );
    }

    // PUT/PATCH /api/sub-categories/{sub_category}
    public function update(
        UpdateSubCategoryRequest $request,
        SubCategory $subCategory
    ) {
        $subCategory->update($request->validated());

        return response()->json([
            'message' => 'Sub-category updated successfully.',
            'sub_category' => $subCategory->fresh()->load('category'),
        ]);
    }

    // DELETE /api/sub-categories/{sub_category}
    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();

        return response()->json([
            'message' => 'Sub-category deleted successfully.',
        ]);
    }
}