<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with([
            'store:id,name',
            'user:id,name',
            'category:id,name',
            'brand:id,name',
            'vendor:id,name',
            'images:id,product_id,image'
        ])->get();

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with([
            'store:id,name',
            'user:id,name',
            'category:id,name',
            'brand:id,name',
            'vendor:id,name',
            'images:id,product_id,image'
        ])->findOrFail($id);

        return response()->json($product);
    }

    public function store(Request $request)
    {
        Log::info($request->all());
        Log::info('Request files:', $request->files->all());
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|integer|exists:stores,id',
            'user_id' => 'required|integer|exists:users,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query->where('store_id', $request->store_id);
                }),
            ],
            'item_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query->where('store_id', $request->store_id);
                }),
            ],
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'vendor_id' => 'nullable|integer|exists:vendors,id',
            'status' => 'required|in:0,1',
            'images' => 'required|array',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $product = Product::create($validator->validated());

        $image = $request->file('images');
        if ($image) {
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/products', $filename, 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
            ]);
        }


        // Handle image upload
        // if ($request->hasFile('images')) {
        //     foreach ($request->file('images') as $image) {
        //         Log::info('Uploading image: ' . $image->getClientOriginalName());

        //         $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        //         $path = $image->storeAs('uploads/products', $filename, 'public');

        //         Log::info('Saved to path: ' . $path);

        //         ProductImage::create([
        //             'product_id' => $product->id,
        //             'image' => $path,
        //         ]);
        //     }
        // } else {
        //     Log::warning('No images were uploaded');
        // }


        return response()->json(['message' => 'Product created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'store_id' => 'required|integer|exists:stores,id',
            'user_id' => 'required|integer|exists:users,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id)->where(function ($query) use ($request) {
                    return $query->where('store_id', $request->store_id);
                }),
            ],
            'item_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id)->where(function ($query) use ($request) {
                    return $query->where('store_id', $request->store_id);
                }),
            ],
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'vendor_id' => 'nullable|integer|exists:vendors,id',
            'status' => 'required|in:0,1',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $product->update($validator->validated());

        // Handle image upload (append to existing)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/products', $filename, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }

        return response()->json(['message' => 'Product updated successfully']);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete associated images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
            $image->delete();
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
