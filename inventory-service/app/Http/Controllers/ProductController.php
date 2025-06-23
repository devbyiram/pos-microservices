<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use App\Models\VariantAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductAttributeValue;
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
            'images:id,product_id,image',
            'singlevariant:id,product_id,sku,price,stock_quantity,tax,tax_type,discount,discount_type',
            'multiplevariants:id,product_id,sku,price,stock_quantity,tax,tax_type,discount,discount_type',

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
            'images:id,product_id,image',
            'singlevariant:id,product_id,sku,price,stock_quantity,tax,tax_type,discount,discount_type',
            'multiplevariants:id,product_id,sku,price,stock_quantity,tax,tax_type,discount,discount_type',
            'attributeValues.attribute:id,name',
        ])->findOrFail($id);
    $variants = [];
    foreach ($product->multiplevariants as $variant) {
        $variantData = $variant->toArray(); // basic fields

        // Attach attributes to variant
        foreach ($product->attributeValues as $attrVal) {
            if ($attrVal->product_variant_id == $variant->id && $attrVal->attribute) {
                $variantData[$attrVal->attribute->name] = $attrVal->value;
            }
        }

        $variants[] = $variantData;
    }
       return response()->json([
    'product' => $product,
    'variants' => $variants,
]);
    }
    //--------------------------------------------------
    public function store(Request $request)
    {
        Log::info($request->all());

        $rules = [
            'store_id' => 'required|integer|exists:stores,id',
            // Comment for now - Maybe will come in use later
            // 'user_id' => 'required|integer|exists:users,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->where(fn($q) => $q->where('store_id', $request->store_id)),
            ],
            'item_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->where(fn($q) => $q->where('store_id', $request->store_id)),
            ],
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'vendor_id' => 'nullable|integer|exists:vendors,id',
            'status' => 'required|in:0,1',
            'images' => 'required|array',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_type' => 'required|in:single,variable',
// Single Variant Fields
    'sku' => 'required_if:product_type,single|nullable|string|max:255',
    'price' => 'required_if:product_type,single|nullable|numeric|min:0',
    'quantity' => 'required_if:product_type,single|nullable|integer|min:0',
    'tax' => 'nullable|numeric|min:0',
    'tax_type' => 'nullable|in:percentage,fixed',
    'discount_type' => 'nullable|in:percentage,fixed',
    'discount_value' => 'nullable|numeric|min:0',

    // Multiple Variant Fields
    'variants' => 'required_if:product_type,variable|array|min:1',
    'variants.*.sku' => 'required|string|max:255',
    'variants.*.price' => 'required|numeric|min:0',
    'variants.*.stock_quantity' => 'required|integer|min:0',
    'variants.*.tax' => 'nullable|numeric|min:0',
    'variants.*.tax_type' => 'nullable|in:percentage,fixed',
    'variants.*.discount' => 'nullable|numeric|min:0',
    'variants.*.discount_type' => 'nullable|in:percentage,fixed',
];

        $messages = [
            'variants.*.sku.required' => 'The SKU field is required for each variant.',
            'variants.*.price.required' => 'The price field is required for each variant.',
            'variants.*.stock_quantity.required' => 'The stock quantity field is required for each variant.',
            'variants.*.sku.max' => 'The SKU may not be greater than 255 characters.',
            'variants.*.price.numeric' => 'The price must be a valid number.',
            'variants.*.stock_quantity.integer' => 'The stock quantity must be an integer.',
            'variants.*.tax.numeric' => 'The tax must be a valid number.',
            'variants.*.tax_type.in' => 'The tax type must be either fixed or percentage.',
            'variants.*.discount.numeric' => 'The discount must be a valid number.',
            'variants.*.discount_type.in' => 'The discount type must be either fixed or percentage.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($validator->validated());

        // Save Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                $storedPath = $image->storeAs('uploads/products', $filename, 'public');
                $path = Storage::url($storedPath);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }

        // Handle Variants
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
        }else {
       foreach ($request->variants as $variant) {
    $createdVariant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => $variant['sku'],
        'price' => $variant['price'],
        'stock_quantity' => $variant['stock_quantity'],
        'tax' => $variant['tax'] ?? null,
        'tax_type' => $variant['tax_type'] ?? null,
        'discount' => $variant['discount'] ?? null,
        'discount_type' => $variant['discount_type'] ?? null,
    ]);

    // ✅ Loop through custom attributes
    foreach ($variant as $key => $value) {
        if (in_array($key, ['sku', 'price', 'stock_quantity', 'tax', 'tax_type', 'discount', 'discount_type'])) {
            continue;
        }

        $attribute = VariantAttribute::firstOrCreate(['name' => $key]);

        ProductAttributeValue::create([
            'product_id' => $product->id,
            'attribute_id' => $attribute->id,
            'product_variant_id' => $createdVariant->id, // ✅ Now correct
            'value' => $value,
        ]);
    }
}
        }
    

    return response()->json(['message' => 'Product created successfully'], 201);
}





    //----------------------------------------------

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'store_id' => 'required|integer|exists:stores,id',
    //         'user_id' => 'required|integer|exists:users,id',
    //         'name' => [
    //             'required',
    //             'string',
    //             'max:255',
    //             Rule::unique('products')->where(function ($query) use ($request) {
    //                 return $query->where('store_id', $request->store_id);
    //             }),
    //         ],
    //         'item_code' => [
    //             'required',
    //             'string',
    //             'max:255',
    //             Rule::unique('products')->where(function ($query) use ($request) {
    //                 return $query->where('store_id', $request->store_id);
    //             }),
    //         ],
    //         'category_id' => 'nullable|integer|exists:categories,id',
    //         'brand_id' => 'nullable|integer|exists:brands,id',
    //         'vendor_id' => 'nullable|integer|exists:vendors,id',
    //         'status' => 'required|in:0,1',
    //         'images' => 'required|array',
    //         'images.*' => 'file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

    //         'sku' => 'required_if:product_type,single|string|max:255',
    //         'price' => 'required_if:product_type,single|numeric|min:0',
    //         'quantity' => 'required_if:product_type,single|integer|min:0',
    //         'tax' => 'nullable|numeric|min:0',
    //         'tax_type' => 'nullable|in:percentage,fixed',
    //         'discount_type' => 'nullable|in:percentage,fixed',
    //         'discount_value' => 'nullable|numeric|min:0',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    //     }

    //     $product = Product::create($validator->validated());

    //     $images = $request->file('images');
    //     if ($request->hasFile('images')) {
    //         foreach ($images as $image) {
    //             $filename = time() . '_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
    //             $storedPath = $image->storeAs('uploads/products', $filename, 'public');
    //             $path = Storage::url($storedPath);

    //             ProductImage::create([
    //                 'product_id' => $product->id,
    //                 'image'      => $path,
    //             ]);
    //         }
    //     }

    //     if ($request->product_type === 'single') {
    //         ProductVariant::create([
    //             'product_id' => $product->id,
    //             'sku' => $request->sku,
    //             'price' => $request->price,
    //             'stock_quantity' => $request->quantity,
    //             'tax' => $request->tax,
    //             'tax_type' => $request->tax_type,
    //             'discount' => $request->discount_value,
    //             'discount_type' => $request->discount_type,
    //         ]);
    //     }
    //     return response()->json(['message' => 'Product created successfully'], 201);
    // }

    public function update(Request $request, $id)
    {

         DB::beginTransaction();

        try{
        $product = Product::findOrFail($id);

          $rules = [
            'store_id' => 'required|integer|exists:stores,id',
            // Comment for now - Maybe will come in use later
            // 'user_id' => 'required|integer|exists:users,id',
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

        // Single Variant Fields
    'sku' => 'required_if:product_type,single|nullable|string|max:255',
    'price' => 'required_if:product_type,single|nullable|numeric|min:0',
    'quantity' => 'required_if:product_type,single|nullable|integer|min:0',
    'tax' => 'nullable|numeric|min:0',
    'tax_type' => 'nullable|in:percentage,fixed',
    'discount_type' => 'nullable|in:percentage,fixed',
    'discount_value' => 'nullable|numeric|min:0',

    // Multiple Variant Fields
    'variants' => 'required_if:product_type,variable|array|min:1',
    'variants.*.sku' => 'required|string|max:255',
    'variants.*.price' => 'required|numeric|min:0',
    'variants.*.stock_quantity' => 'required|integer|min:0',
    'variants.*.tax' => 'nullable|numeric|min:0',
    'variants.*.tax_type' => 'nullable|in:percentage,fixed',
    'variants.*.discount' => 'nullable|numeric|min:0',
    'variants.*.discount_type' => 'nullable|in:percentage,fixed',
];

       $messages = [
            'variants.*.sku.required' => 'The SKU field is required for each variant.',
            'variants.*.price.required' => 'The price field is required for each variant.',
            'variants.*.stock_quantity.required' => 'The stock quantity field is required for each variant.',
            'variants.*.sku.max' => 'The SKU may not be greater than 255 characters.',
            'variants.*.price.numeric' => 'The price must be a valid number.',
            'variants.*.stock_quantity.integer' => 'The stock quantity must be an integer.',
            'variants.*.tax.numeric' => 'The tax must be a valid number.',
            'variants.*.tax_type.in' => 'The tax type must be either fixed or percentage.',
            'variants.*.discount.numeric' => 'The discount must be a valid number.',
            'variants.*.discount_type.in' => 'The discount type must be either fixed or percentage.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($validator->validated());


        if ($request->has('existing_images')) {
            $existingIds = $request->input('existing_images'); // array of IDs to keep
        } else {
            $existingIds = [];
        }

        // 🧹 Delete old images not in existing_images[]
        $productImages = ProductImage::where('product_id', $product->id)->get();
        foreach ($productImages as $productImage) {
            if (!in_array($productImage->id, $existingIds)) {
                $imagePath = str_replace('/storage/', '', $productImage->image);
                Storage::disk('public')->delete($imagePath);
                $productImage->delete();
            }
        }

        // 📤 Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if (!$image instanceof \Illuminate\Http\UploadedFile) {
                    continue; // 👈 Prevents the "Invalid resource type: array" error
                }

                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $storedPath = $image->storeAs('uploads/products', $filename, 'public');
                $path = Storage::url($storedPath);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }


// Clear old variants and attributes if variable
        if ($request->product_type === 'variable') {
            // Delete old variants and attributes
            ProductVariant::where('product_id', $product->id)->delete();
            ProductAttributeValue::where('product_id', $product->id)->delete();

            foreach ($request->variants as $variantData) {
                // Create new variant
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'],
                    'price' => $variantData['price'],
                    'stock_quantity' => $variantData['stock_quantity'],
                    'tax' => $variantData['tax'] ?? null,
                    'tax_type' => $variantData['tax_type'] ?? null,
                    'discount' => $variantData['discount'] ?? null,
                    'discount_type' => $variantData['discount_type'] ?? null,
                ]);

                // Save attribute values (keys not in default fields)
                $defaultFields = ['sku', 'price', 'stock_quantity', 'tax', 'tax_type', 'discount', 'discount_type'];

                foreach ($variantData as $key => $value) {
                    if (!in_array($key, $defaultFields)) {
                        // Get attribute_id by name
                        $attribute = VariantAttribute::firstOrCreate(['name' => $key]);

                     ProductAttributeValue::create([
    'product_id'         => $product->id,
    'product_variant_id' => $variant->id, // 🔥 new
    'attribute_id'       => $attribute->id,
    'value'              => $value
]);
                    }
                }
            }

        } else {
            // For single product
            $variant = ProductVariant::where('product_id', $product->id)->first();

            if ($variant) {
                $variant->update([
                    'sku'            => $request->sku,
                    'price'          => $request->price,
                    'stock_quantity' => $request->quantity,
                    'tax'            => $request->tax,
                    'tax_type'       => $request->tax_type,
                    'discount'       => $request->discount_value,
                    'discount_type'  => $request->discount_type,
                ]);
            } else {
                ProductVariant::create([
                    'product_id' => $product->id,
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

        DB::commit();

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error updating product: ' . $e->getMessage());
    }
}


   public function destroy($id)
{
    $product = Product::with('images')->findOrFail($id);

    DB::beginTransaction();

    try {
        // Delete product images
        $product->images->each(function ($image) {
            if ($image->image) {
                $storagePath = str_replace('/storage/', '', $image->image);
                Storage::disk('public')->delete($storagePath);
            }
            $image->delete();
        });

        // Delete product variants
        ProductVariant::where('product_id', $product->id)->delete();

        // Delete the product
        $product->delete();

        DB::commit();

        return response()->json(['message' => 'Product deleted successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Failed to delete product', 'error' => $e->getMessage()], 500);
    }
}
}
