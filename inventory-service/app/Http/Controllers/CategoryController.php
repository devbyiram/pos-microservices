<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('store:id,name')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->where(function ($query) use ($request) {
                    return $query->where('store_id', $request->store_id);
                }),
            ],
            'store_id' => 'required|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        Category::create($request->all());

        return response()->json(['message' => 'Category created successfully'], 201);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->ignore($category->id)
                    ->where(function ($query) use ($request) {
                        return $query->where('store_id', $request->store_id);
                    }),
            ],
            'store_id' => 'required|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update($request->all());

        return response()->json(['message' => 'Category updated successfully']);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
