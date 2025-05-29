<?php

namespace App\Services;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
       $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        
        //attach all platform by default
        $allPlatformIds = Platform::pluck('id');
        $user->activePlatforms()->attach($allPlatformIds);

        $token = $user->createToken('authToken')->plainTextToken;



        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data): array
    {
      
        $password =$data['password'];
        $user = User::select('*')->where('email',$data['email'])->first();
        if ($user && (Hash::check($password, $user->password))) { 
          $token = $user->createToken('auth_token')->plainTextToken;
            return [
                'user' => $user,
                'token' => $token,
            ];

        }
        throw new \Exception('Invalid credentials.');

    }

    public function profile(User $user): User
    {
        return $user;
    }

    public function logout(): bool
    {
        $user = Auth::user();
        if ($user) {
            $user->currentAccessToken()->delete();
            return true;
        }
        return false;
    }
}
