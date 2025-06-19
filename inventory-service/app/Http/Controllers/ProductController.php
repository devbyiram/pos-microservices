<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

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
            'images:id,product_id,image',
            
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
            'images.*' => 'file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'sku' => 'required_if:product_type,single|string|max:255',
            'price' => 'required_if:product_type,single|numeric|min:0',
            'quantity' => 'required_if:product_type,single|integer|min:0',
            'tax' => 'nullable|numeric|min:0',
            'tax_type' => 'nullable|in:percentage,fixed',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $product = Product::create($validator->validated());

        $images = $request->file('images');
        if ($request->hasFile('images')) {
            foreach ($images as $image) {
                $filename = time() . '_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                $storedPath = $image->storeAs('uploads/products', $filename, 'public');
                $path = Storage::url($storedPath);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $path,
                ]);
            }
        }

        if ($request->product_type === 'single') {
            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $request->sku,
                'price' => $request->price,
                'stock_quantity' => $request->quantity,
                'tax' => $request->tax,
                'tax_type' => $request->tax_type,
                'discount' => $request->discount_value,
                'discount_type' => $request->discount_type,
            ]);
        }
        return response()->json(['message' => 'Product created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        Log::info('Files:', $request->allFiles());

        $validator = Validator::make($request->all(), [
            'store_id' => 'required|integer|exists:stores,id',
            'user_id' => 'required|integer|exists:users,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id)->where(fn($query) => $query->where('store_id', $request->store_id)),
            ],
            'item_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id)->where(fn($query) => $query->where('store_id', $request->store_id)),
            ],
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'vendor_id' => 'nullable|integer|exists:vendors,id',
            'status' => 'required|in:0,1',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'sku' => 'required_if:product_type,single|string|max:255',
            'price' => 'required_if:product_type,single|numeric|min:0',
            'quantity' => 'required_if:product_type,single|integer|min:0',
            'tax' => 'nullable|numeric|min:0',
            'tax_type' => 'nullable|in:fixed,percentage',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $product->update($validator->validated());

        // ✅ Remove old images if new ones are provided
        if ($request->hasFile('images')) {
            $productImages = ProductImage::where('product_id', $product->id)->get();
            foreach ($productImages as $productImage) {
                $imagePath = str_replace('/storage/', '', $productImage->image);
                Storage::disk('public')->delete($imagePath);
                $productImage->delete();
            }

            // ✅ Upload new ones
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $storedPath = $image->storeAs('uploads/products', $filename, 'public');
                $path = Storage::url($storedPath);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }

        // ✅ Handle Product Variant if single product
        if ($request->product_type === 'single') {
            // Update if exists, otherwise create new
            $variant = ProductVariant::where('product_id', $product->id)->first();

            if ($variant) {
                $variant->update([
                    'sku'            => $request->sku,
                    'price'          => $request->price,
                    'stock_quantity' => $request->quantity,
                    'tax'            => $request->tax,
                    'tax_type'       => $request->tax_type,
                    'discount'       => $request->discount,
                    'discount_type'  => $request->discount_type,
                ]);
            } else {
                ProductVariant::create([
                    'sku' => $request->sku,
                    'price' => $request->price,
                    'stock_quantity' => $request->stock_quantity,
                    'tax' => $request->tax,
                    'tax_type' => $request->tax_type,
                    'discount' => $request->discount,
                    'discount_type' => $request->discount_type,
                ]);
            }
        }

        return response()->json(['message' => 'Product updated successfully']);
    }


public function destroy($id)
{
    $product = Product::findOrFail($id);

    // Delete associated images from storage and DB
    foreach ($product->images as $image) {
        $storagePath = str_replace('/storage/', '', $image->image);
        Storage::disk('public')->delete($storagePath);
        $image->delete();
    }

    // Delete product variants
    ProductVariant::where('product_id', $product->id)->delete();

    // Delete the product
    $product->delete();

    return response()->json(['message' => 'Product deleted successfully']);
}
}
