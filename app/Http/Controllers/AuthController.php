<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request): array{
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::default()]
        ]);

        /**
         * @var User $user
         */
        $user = User::create(array_filter($credentials, fn ($key) => $key !== 'confirmation_password', ARRAY_FILTER_USE_KEY));
    

        return [
            'message' => 'User Created',
            'status' => 200,
            'token' => $user->createToken('register-token')->plainTextToken
        ];
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', Password::default()]
        ]);

        /**
         * @var User $user
         */

        $user = User::where('email', $credentials['email'])->first();

        if(! $user){
            return [
                'message' => 'Email or password incorrect',
                'status' => 422,
            ];
        }

        if(Hash::check($credentials['password'], $user->password)){
            
            return [
                'message' => 'Authenticated',
                'status' => 200,
                'token' => $user->createToken('authentication')->plainTextToken
            ];
        }
    }

    public function logout(Request $request){
        
        /**
         * @var User $user
         * @var PersonnalAccessToken $token
         */
        $user = $request->user();

        $user->tokens()->delete();

        return [
            'message' => $user->name .  ' unauthenticated',
            'status' => 200
        ];
    }
}
