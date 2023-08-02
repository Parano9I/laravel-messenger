<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Resources\V1\TokenResource;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);

        if ($request->hasFile('avatar')) {
            $fileName = Storage::disk('avatars')->put(null, $request->file('avatar'));
            $user->avatar = $fileName;
        }

        $user->save();
        $token = $user->createToken('bearer')->plainTextToken;

        return response()->json([
            'data' => [
                'user'  => new UserResource($user),
                'token' => new TokenResource((object)['token' => $token])
            ]
        ]);
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        if(!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email & Password does not match with our record.',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('bearer')->plainTextToken;

        return response()->json([
            'data' => [
                'user'  => new UserResource($user),
                'token' => new TokenResource((object)['token' => $token])
            ]
        ]);
    }
}
