<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password']
        ];

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorised'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => auth()->user(),
            'token' => $token
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function getActiveUser()
    {
        $activeUser = Auth::user();

        return response()->json($activeUser);
    }
}
