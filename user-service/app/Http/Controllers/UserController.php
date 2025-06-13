<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // GET /users
    public function index()
    {
        return response()->json(User::all());
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:6',
            'store_id'  => 'required|integer|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $validated = $validator->validated();

            $validated['password'] = bcrypt($validated['password']);

            // Create user
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password']
            ]);

            // Attach store
            $user->stores()->attach($validated['store_id']);

            return response()->json(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'User creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /users/{id}
    public function show($id)
    {
        $user = User::with('stores')->findOrFail($id);
         return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'stores' => $user->stores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
            ];
        })->toArray(),
    ]);
    }

    // PUT /users/{id}



    public function update(Request $request, $id)
    {
        // Load user with existing stores (optional)
        $user = User::with('stores')->findOrFail($id);

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'store_id' => 'required|exists:stores,id',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get validated input
        $validated = $validator->validated();

        // Update user fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        // Sync store to pivot table (user_store)
        $user->stores()->sync([$validated['store_id']]);

        // Return success response

        return response()->json([
            'message' => 'User updated successfully',
        ], 200);
    }



    // DELETE /users/{id}
 public function destroy($id)
{
    $user = User::findOrFail($id);

    // Optional: detach store relationships if you want to clean up pivot
    $user->stores()->detach();

    $user->delete();

    return response()->json([
        'message' => 'User deleted successfully'
    ]);
}
}