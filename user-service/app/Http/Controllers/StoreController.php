<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
  public function index()
{
    $stores = Store::with('users:id,name')->get();
    return response()->json($stores);
}

    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255|unique:stores,name',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    Store::create([
        'name' => $request->name,
    ]);
        return response()->json(['message' => 'Store created successfully'], 201);
    }

    public function show(Store $store)
    {
        return response()->json($store);
    }

    public function update(Request $request, Store $store)
    {
        $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:100|unique:stores,name,' . $store->id,
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation errors occurred.',
            'errors' => $validator->errors()
        ], 422);
    }

    $store->update([
        'name' => $request->input('name'),
    ]);

        return response()->json(['message' => 'Store updated successfully']);
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return response()->json(['message' => 'Store deleted successfully']);
    }
}
