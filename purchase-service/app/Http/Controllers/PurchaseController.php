<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with([
            'store:id,name',
            'user:id,name',
            'vendor:id,name',
            'items.product:id'
        ])->get();

        return response()->json($purchases);
    }

    public function show($id)
    {
        $purchase = Purchase::with([
            'store:id,name',
            'user:id,name',
            'vendor:id,name',
            'items.product:id,name'
        ])->findOrFail($id);

        return response()->json($purchase);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'user_id' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $purchase = Purchase::create([
            'store_id' => $data['store_id'],
            'user_id' => $data['user_id'],
            'vendor_id' => $data['vendor_id'],
            'purchase_date' => $data['purchase_date'],
            'total_amount' => $data['total_amount']
        ]);

        foreach ($data['items'] as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        return response()->json(['message' => 'Purchase created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'user_id' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $purchase->update([
            'store_id' => $data['store_id'],
            'user_id' => $data['user_id'],
            'vendor_id' => $data['vendor_id'],
            'purchase_date' => $data['purchase_date'],
            'total_amount' => $data['total_amount']
        ]);

        // Remove old items and insert new
        $purchase->items()->delete();

        foreach ($data['items'] as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        return response()->json(['message' => 'Purchase updated successfully']);
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->items()->delete();
        $purchase->delete();

        return response()->json(['message' => 'Purchase deleted successfully']);
    }
}
