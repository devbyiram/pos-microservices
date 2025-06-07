<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // if (!$user || $credentials['password'] !== $user->password) {
            //     return response()->json(['error' => 'Invalid credentials'], 401);
            // }

            $privateKey = file_get_contents(storage_path('oauth-private.key'));

            $payload = [
                'iss' => 'auth-service',             // Issuer
                'sub' => $user->id,                   // Subject (user ID)
                'email' => $user->email,
                'iat' => time(),                      // Issued at
                'exp' => time() + 3600,               // Expires in 1 hour
            ];

            $jwt = JWT::encode($payload, $privateKey, 'RS256');

            return response()->json([
                'access_token' => $jwt,
                'token_type' => 'bearer',
            ]);
        } catch (\Exception $e) {
            Log::error('Login error', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }


    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

   public function dashboard()
    {
        return response()->json(['here' => 'is me']);

    }
}
