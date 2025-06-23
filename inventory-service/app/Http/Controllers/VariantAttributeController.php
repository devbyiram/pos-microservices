<?php

namespace App\Http\Controllers;

use App\Models\VariantAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VariantAttributeController extends Controller
{
    // Get all variant attributes
    public function index()
    {
        return response()->json(VariantAttribute::all());
    }

    // Store new variant attribute
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attribute = VariantAttribute::create($validator->validated());

        return response()->json([
            'message' => 'Variant attribute created successfully.',
        ], 201);
    }

    // Show a specific variant attribute
    public function show($id)
    {
        $attribute = VariantAttribute::findOrFail($id);

        return response()->json($attribute);
    }

    // Update a variant attribute
    public function update(Request $request, $id)
    {
        $attribute = VariantAttribute::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'value' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attribute->update($validator->validated());

        return response()->json([
            'message' => 'Variant attribute updated successfully.',
        ]);
    }

    // Delete a variant attribute
    public function destroy($id)
    {
        $attribute = VariantAttribute::findOrFail($id);
        $attribute->delete();

        return response()->json(['message' => 'Variant attribute deleted successfully.']);
    }
}
