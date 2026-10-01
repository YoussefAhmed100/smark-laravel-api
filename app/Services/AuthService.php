<?php

namespace App\Services;

use App\DTOs\Auth\RegisterUserDTO;
use App\Enums\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * @return array{user: User, token: string}
     *
     * @throws ValidationException
     */
    public function register(RegisterUserDTO $dto): array
    {
        $this->ensureEmailIsUnique($dto->email);

        try {
            return DB::transaction(function () use ($dto) {
                $tenant = Tenant::create([
                    'name' => Str::before($dto->email, '@')."'s workspace",
                ]);

                $user = $tenant->users()->create([
                    'email'    => $dto->email,
                    'password' => $dto->password,
                    'role'     => Role::Admin,
                ]);

                return [
                    'user'  => $user,
                    'token' => JWTAuth::fromUser($user),
                ];
            });
        } catch (UniqueConstraintViolationException) {
            throw $this->emailTakenException();
        }
    }

    private function ensureEmailIsUnique(string $email): void
    {
        if (User::where('email', $email)->exists()) {
            throw $this->emailTakenException();
        }
    }

    private function emailTakenException(): ValidationException
    {
        return ValidationException::withMessages([
            'email' => ['This email is already registered.'],
        ]);
    }
}