<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $data = $request->only('name', 'email', 'password');
            $user = User::create($data);
            $token = $user->createToken('RegisterToken')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Register successfully!',
                'token_type' => 'Bearer',
                'token' => $token,
                'data' => $user
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'message' => 'Backend Error',
                'erores' => $th->getMessage()
            ]);
        }
    }
    public function signin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $user = User::where('email', $request->email)->first();
            if (!$user && !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 401);
            }
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email'
                ], 401);
            }

            $token = $user->createToken('LoginToken')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login successfully!',
                'token_type' => 'Bearer',
                'token' => $token,
                'data' => $user
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'message' => 'Backend Error',
                'erores' => $th->getMessage()
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful.',
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
