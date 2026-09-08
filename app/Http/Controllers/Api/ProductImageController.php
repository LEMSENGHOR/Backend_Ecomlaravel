<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    // POST /api/products/{product}/images
    public function store(
        StoreProductImageRequest $request,
        Product $product
    ) {
        $data = $request->validated();

        // Upload file
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');

            $data['image_url'] = url(
                Storage::disk('public')->url($path)
            );
        }

        // image is not a database column
        unset($data['image']);

        // Get product_id from URL
        $data['product_id'] = $product->id;

        // Make this image primary
        if ($request->boolean('is_primary')) {
            $product->images()->update([
                'is_primary' => false,
            ]);
        }

        $productImage = ProductImage::create($data);

        return response()->json([
            'message' => 'Product image created successfully.',
            'product_image' => $productImage,
        ], 201);
    }

    // DELETE /api/product-images/{productImage}
    // public function destroy(ProductImage $productImage)
    // {
    //     $productImage->delete();

    //     return response()->json([
    //         'message' => 'Product image deleted successfully.',
    //     ], 200);
    // }
    // DELETE /api/product-images/{productImage}
public function destroy(ProductImage $productImage)
{
    $productImage->delete();

    return response()->json([
        'message' => 'Product image deleted successfully.',
    ], 200);
}
}