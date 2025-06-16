<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseItemController extends Controller
{
    public function index()
    {
        $items = PurchaseItem::with(['purchase:id,purchase_date,total_amount', 'product:id,name'])->get();
        return response()->json($items);
    }

    public function show($id)
    {
        $item = PurchaseItem::with(['purchase:id,purchase_date,total_amount', 'product:id,name'])->findOrFail($id);
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_id' => 'required|exists:purchases,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        PurchaseItem::create($validator->validated());

        return response()->json(['message' => 'Purchase item created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $item = PurchaseItem::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'purchase_id' => 'required|exists:purchases,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $item->update($validator->validated());

        return response()->json(['message' => 'Purchase item updated successfully']);
    }

    public function destroy($id)
    {
        $item = PurchaseItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Purchase item deleted successfully']);
    }
}

