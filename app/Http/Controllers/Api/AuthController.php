<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(RegistrationRequest $request): JsonResponse
    {

        $response = $this->authService->register($request->validated());

        return $this->responseSuccess('Registered Successfully ',$response, self::STATUS_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
       try {
        $response = $this->authService->login($request->validated());
            
        }catch (\Exception $e) {
           return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('Login Successfully ',$response);
    }

    public function logout(): JsonResponse
    {
        try {
        $response = $this->authService->logout();
            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
        return $this->responseSuccess('Logged out successfully ',$response);
    }

    public function profile(): JsonResponse
    {
        try {
        $response = Auth::user();
            
        }catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
        return $this->responseSuccess('Success ',$response);
    }
}

