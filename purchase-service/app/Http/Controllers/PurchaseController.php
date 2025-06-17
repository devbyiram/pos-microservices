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
            'items.product:id,name'
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
        'vendor_id' => 'required|exists:vendors,id',
        'purchase_date' => 'required|date',
        'shipping' => 'nullable|numeric|min:0',
        'status' => 'required|in:Received,Pending',
        'payment_status' => 'required|in:Paid,Unpaid',

        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.purchase_price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0',
        'items.*.tax' => 'nullable|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();

    $orderTax = 0;
    $orderDiscount = 0;
    $totalAmount = 0;

    $purchase = Purchase::create([
        'store_id' => $data['store_id'],
        'user_id' => null,
        'vendor_id' => $data['vendor_id'],
        'purchase_date' => $data['purchase_date'],
        'shipping' => $data['shipping'] ?? 0,
        'status' => $data['status'],
        'payment_status' => $data['payment_status'],
        'order_tax' => 0,
        'order_discount' => 0,
        'total_amount' => 0,
    ]);

    foreach ($data['items'] as $item) {
        $purchasePrice = $item['purchase_price'];
        $discount = $item['discount'] ?? 0;
        $tax = $item['tax'] ?? 0;
        $quantity = $item['quantity'];

        if ($discount > $purchasePrice) {
            return response()->json([
                'message' => 'Discount cannot be greater than purchase price for a product.',
                'product_id' => $item['product_id']
            ], 422);
        }

        // Calculations
        $priceAfterDiscount = $purchasePrice - $discount;
        $taxAmount = ($priceAfterDiscount * $tax) / 100; // per unit tax
        $unitCost = $priceAfterDiscount + $taxAmount;
        $totalCost = $unitCost * $quantity;

        $orderTax += $taxAmount * $quantity;
        $orderDiscount += $discount * $quantity;
        $totalAmount += $totalCost;

        // Save item
        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $item['product_id'],
            'quantity' => $quantity,
            'purchase_price' => $purchasePrice,
            'discount' => $discount,
            'tax' => $tax,
            'tax_amount' => $taxAmount,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
        ]);
    }

    // Final update
    $purchase->update([
        'order_tax' => $orderTax,
        'order_discount' => $orderDiscount,
        'total_amount' => $totalAmount + ($data['shipping'] ?? 0),
    ]);

    return response()->json(['message' => 'Purchase created successfully'], 201);
}


    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'required|date',
            'shipping' => 'nullable|numeric|min:0',
            'status' => 'required|in:Received,Pending',
            'payment_status' => 'required|in:Paid,Unpaid',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $orderTax = 0;
        $orderDiscount = 0;
        $totalAmount = 0;

        $purchase->items()->delete();

        foreach ($data['items'] as $item) {
            $purchasePrice = $item['purchase_price'];
            $discount = $item['discount'] ?? 0;
            $tax = $item['tax'] ?? 0;

            if ($discount > $purchasePrice) {
                return response()->json([
                    'message' => 'Discount cannot be greater than purchase price for a product.',
                    'product_id' => $item['product_id']
                ], 422);
            }

            $priceAfterDiscount = $purchasePrice - $discount;
            $taxAmount = ($priceAfterDiscount * $tax) / 100;
            $unitCost = $priceAfterDiscount + $taxAmount;
            $totalCost = $unitCost * $item['quantity'];

            $orderTax += $taxAmount * $item['quantity'];
            $orderDiscount += $discount * $item['quantity'];
            $totalAmount += $totalCost;

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'purchase_price' => $purchasePrice,
                'discount' => $discount,
                'tax' => $tax,
                'tax_amount' => $taxAmount,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
        }

        $purchase->update([
            'store_id' => $data['store_id'],
            'user_id' => null,
            'vendor_id' => $data['vendor_id'],
            'purchase_date' => $data['purchase_date'],
            'shipping' => $data['shipping'] ?? 0,
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'order_tax' => $orderTax,
            'order_discount' => $orderDiscount,
            'total_amount' => $totalAmount + ($data['shipping'] ?? 0),
        ]);

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
