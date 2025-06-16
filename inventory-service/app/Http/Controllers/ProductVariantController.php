<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductVariantController extends Controller
{
    // Get all product variants
    public function index()
    {
        return response()->json(ProductVariant::with('product:id,name')->get());
    }

    // Create a new product variant
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|string|unique:product_variants,sku',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'tax' => 'nullable|numeric',
            'tax_type' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $variant = ProductVariant::create($validated);

        return response()->json([
            'message' => 'Product variant created successfully.',
        ], 201);
    }

    // Show a specific product variant
    public function show($id)
    {
        $variant = ProductVariant::with('product:id,name')->findOrFail($id);
        return response()->json($variant);
    }

    // Update a product variant
    public function update(Request $request, $id)
    {
        $variant = ProductVariant::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'product_id' => 'sometimes|exists:products,id',
            'sku' => 'sometimes|string|unique:product_variants,sku,' . $id,
            'price' => 'sometimes|numeric',
            'stock_quantity' => 'sometimes|integer',
            'tax' => 'nullable|numeric',
            'tax_type' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $variant->update($validated);

        return response()->json([
            'message' => 'Product variant updated successfully.',
        ]);
    }

    // Delete a product variant
    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();

        return response()->json([
            'message' => 'Product variant deleted successfully.'
        ]);
    }
}
