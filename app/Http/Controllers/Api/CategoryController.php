<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index()
    {
        return response()->json(
            Category::query()->paginate(20)
        );
    }

    // POST /api/categories
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category,
        ], 201);
    }

    // GET /api/categories/{category}
    public function show(Category $category)
    {
        return response()->json(
            $category->load('subCategories')
        );
    }

    // PUT/PATCH /api/categories/{category}
    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ) {
        $category->update($request->validated());

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category->fresh(),
        ]);
    }

    // DELETE /api/categories/{category}
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }
}