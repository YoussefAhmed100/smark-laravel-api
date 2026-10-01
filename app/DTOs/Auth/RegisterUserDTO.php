<?php

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\RegisterRequest;

final readonly class RegisterUserDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        $data = $request->validated();

        return new self(
            email: $data['email'],
            password: $data['password'],
        );
    }
}