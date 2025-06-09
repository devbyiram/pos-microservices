<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /users
    public function index()
    {
        return response()->json(User::all());
    }

    // POST /users
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'store_id' => 'required|array', // store IDs for pivot table
            'store_id.*' => 'exists:stores,id'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        $user->stores()->attach($validated['store_id']);
        return response()->json(['message' => 'User created successfully','user' => $user->load('stores')], 201);
    }

    // GET /users/{id}
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // PUT /users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update($request->only(['name', 'email']));
        return response()->json(['message' => 'User updated successfully']);
    }

    // DELETE /users/{id}
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
