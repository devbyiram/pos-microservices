<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    $rules = [
        'store_id'              => 'required|exists:stores,id',
        'vendor_id'             => 'required|exists:vendors,id',
        'purchase_date'         => 'required|date',
        'status'                => 'required|in:Received,Pending',
        'payment_status'        => 'required|in:Paid,Unpaid',
        'order_tax'             => 'nullable|numeric|min:0',
        'order_discount'        => 'nullable|numeric|min:0',
        'shipping'              => 'nullable|numeric|min:0',
        'total_amount'          => 'required|numeric|min:0',

        'items'                     => 'required|array|min:1',
        'items.*.product_id'        => 'required|exists:products,id',
        'items.*.quantity'          => 'required|integer|min:1',
        'items.*.purchase_price'    => 'required|numeric|min:1',
        'items.*.discount'          => 'nullable|numeric|min:0',
        'items.*.tax'               => 'nullable|numeric|min:0',
        'items.*.tax_amount'        => 'required_with:items.*.tax|numeric|min:0',
        'items.*.unit_cost'         => 'required|numeric|min:0',
        'items.*.total_cost'        => 'required|numeric|min:0',
    ];

    $messages = [
        'store_id.required'          => 'You must choose a store.',
        'vendor_id.required'         => 'You must choose a vendor.',

        'order_tax.numeric'          => 'Order tax must be a number.',
        'order_tax.min'              => 'Order tax must be valid.',

        'order_discount.numeric'     => 'Order discount must be a number.',
        'order_discount.min'         => 'Order discount must be valid.',

        'shipping.numeric'           => 'Shipping must be a number.',
        'shipping.min'               => 'Shipping must be valid.',

        'total_amount.required'      => 'Total amount is required.',
        'total_amount.numeric'       => 'Total amount must be a number.',
        'total_amount.min'           => 'Total amount must be valid.',

        'items.required'             => 'Please add at least one line-item.',
        'items.min'                  => 'You need at least one line-item.',

        'items.*.product_id.required'        => 'Product is required',
        'items.*.product_id.exists'          => 'The selected product does not exist.',

        'items.*.quantity.required'          => 'Quantity is required for each line-item.',
        'items.*.quantity.integer'           => 'Quantity must be a whole number.',
        'items.*.quantity.min'               => 'Quantity must be valid.',

        'items.*.purchase_price.required'    => 'Purchase price is required.',
        'items.*.purchase_price.numeric'     => 'Purchase price must be a number.',
        'items.*.purchase_price.min'         => 'Purchase price must be valid.',

        'items.*.discount.numeric'           => 'Discount must be a number.',
        'items.*.discount.min'               => 'Discount must be valid.',

        'items.*.tax.numeric'                => 'Tax must be a number.',
        'items.*.tax.min'                    => 'Tax must be valid.',

        'items.*.tax_amount.required_with'   => 'Tax amount is required when tax is present.',
        'items.*.tax_amount.numeric'         => 'Tax amount must be a number.',
        'items.*.tax_amount.min'             => 'Tax amount must be valid.',

        'items.*.unit_cost.required'         => 'Unit cost is required.',
        'items.*.unit_cost.numeric'          => 'Unit cost must be a number.',
        'items.*.unit_cost.min'              => 'Unit cost must be valid.',

        'items.*.total_cost.required'        => 'Total cost is required.',
        'items.*.total_cost.numeric'         => 'Total cost must be a number.',
        'items.*.total_cost.min'             => 'Total cost must be valid.',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();

    try {
        DB::beginTransaction();

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
            'order_tax' => 0, // temporarily 0
            'order_discount' => 0,
            'total_amount' => 0,
        ]);

        foreach ($data['items'] as $item) {
            $purchasePrice = $item['purchase_price'];
            $discount = $item['discount'] ?? 0;
            $tax = $item['tax'] ?? 0;

            if ($discount > $purchasePrice) {
                DB::rollBack();
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
                'tax_percent' => $tax,
                'tax_amount' => $taxAmount,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
        }

        $purchase->update([
            'order_tax' => $orderTax,
            'order_discount' => $orderDiscount,
            'total_amount' => $totalAmount + ($data['shipping'] ?? 0),
        ]);

        DB::commit();

        return response()->json(['message' => 'Purchase created successfully'], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Purchase creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'message' => 'Failed to create purchase',
            'error' => $e->getMessage()
        ], 500);
    }
}



    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $rules = [
            'store_id'              => 'required|exists:stores,id',
            'vendor_id'             => 'required|exists:vendors,id',
            'purchase_date'         => 'required|date',
            'status'                => 'required|in:Received,Pending',
            'payment_status'        => 'required|in:Paid,Unpaid',
            'order_tax'             => 'nullable|numeric|min:0',
            'order_discount'        => 'nullable|numeric|min:0',
            'shipping'              => 'nullable|numeric|min:0',
            'total_amount'          => 'required|numeric|min:0',

            /* items */
            'items'                     => 'required|array|min:1',
            'items.*.product_id'        => 'required|exists:products,id',
            'items.*.quantity'          => 'required|integer|min:1',
            'items.*.purchase_price'    => 'required|numeric|min:1',
            'items.*.discount'          => 'nullable|numeric|min:0',
            'items.*.tax'               => 'nullable|numeric|min:0',
            'items.*.tax_amount'        => 'required_with:items.*.tax|numeric|min:0',
            'items.*.unit_cost'         => 'required|numeric|min:0',
            'items.*.total_cost'        => 'required|numeric|min:0',
        ];

        $messages = [
            'store_id.required'          => 'You must choose a store.',
            'vendor_id.required'         => 'You must choose a vendor.',

            'order_tax.numeric'          => 'Order tax must be a number.',
            'order_tax.min'              => 'Order tax must be valid.',

            'order_discount.numeric'     => 'Order discount must be a number.',
            'order_discount.min'         => 'Order discount must be valid.',

            'shipping.numeric'           => 'Shipping must be a number.',
            'shipping.min'               => 'Shipping must be valid.',

            'total_amount.required'      => 'Total amount is required.',
            'total_amount.numeric'       => 'Total amount must be a number.',
            'total_amount.min'           => 'Total amount must be valid.',

            'items.required'             => 'Please add at least one line-item.',
            'items.min'                  => 'You need at least one line-item.',

            'items.*.product_id.required'        => 'Product is required',
            'items.*.product_id.exists'          => 'The selected product does not exist.',

            'items.*.quantity.required'          => 'Quantity is required for each line-item.',
            'items.*.quantity.integer'           => 'Quantity must be a whole number.',
            'items.*.quantity.min'               => 'Quantity must be valid.',

            'items.*.purchase_price.required'    => 'Purchase price is required.',
            'items.*.purchase_price.numeric'     => 'Purchase price must be a number.',
            'items.*.purchase_price.min'         => 'Purchase price must be valid.',

            'items.*.discount.numeric'           => 'Discount must be a number.',
            'items.*.discount.min'               => 'Discount must be valid.',

            'items.*.tax.numeric'                => 'Tax must be a number.',
            'items.*.tax.min'                    => 'Tax must be valid.',

            'items.*.tax_amount.required_with'   => 'Tax amount is required when tax is present.',
            'items.*.tax_amount.numeric'         => 'Tax amount must be a number.',
            'items.*.tax_amount.min'             => 'Tax amount must be valid.',

            'items.*.unit_cost.required'         => 'Unit cost is required.',
            'items.*.unit_cost.numeric'          => 'Unit cost must be a number.',
            'items.*.unit_cost.min'              => 'Unit cost must be valid.',

            'items.*.total_cost.required'        => 'Total cost is required.',
            'items.*.total_cost.numeric'         => 'Total cost must be a number.',
            'items.*.total_cost.min'             => 'Total cost must be valid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

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
                'tax_percent' => $tax,
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
