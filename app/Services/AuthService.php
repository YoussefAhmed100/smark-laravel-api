<?php

namespace App\Services;

use App\DTOs\Auth\RegisterUserDTO;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * @return array{user: \App\Models\User, token: string}
     */
    public function register(RegisterUserDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $localPart = Str::before($dto->email, '@');

            $tenant = Tenant::create([
                'name' => "{$localPart}'s workspace",
            ]);

            $user = $tenant->users()->create([
                'email'    => $dto->email,
                'password' => Hash::make($dto->password),
                'role'     => 'admin',
            ]);

            $token = $user->createToken('auth')->plainTextToken;

            return ['user' => $user, 'token' => $token];
        });
    }
}