<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\RegisterUserDTO;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterUserDTO::fromRequest($request);

        ['user' => $user, 'token' => $token] = $this->authService->register($dto);

        return response()->json([
            'user' => [
                'id'        => $user->id,
                'email'     => $user->email,
                'role'      => $user->role,
                'tenant_id' => $user->tenant_id,
            ],
            'token' => $token,
        ], 201);
    }
}