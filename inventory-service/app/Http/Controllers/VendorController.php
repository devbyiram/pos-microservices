<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('store:id,name')->get();
        return response()->json($vendors);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vendors', 'name')->where(function ($query) use ($request) {
                    return $query->where('store_id', $request->store_id);
                }),
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        Vendor::create($validator->validated());

        return response()->json(['message' => 'Vendor created successfully'], 201);
    }

    public function show(Vendor $vendor)
    {
        return response()->json($vendor->load('store:id,name'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vendors', 'name')->where(function ($query) use ($request) {
                    return $query->where('store_id', $request->store_id);
                }),
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $vendor->update($validator->validated());

        return response()->json(['message' => 'Vendor updated successfully']);
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return response()->json(['message' => 'Vendor deleted successfully']);
    }
}
