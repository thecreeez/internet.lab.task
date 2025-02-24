<?php

namespace App\Controller\Api\User\input;

class UserPostRequest
{
    public function __construct(
        public string $username,
        public string $password,
        public string $passwordConfirmation,
        public string $description,
    )
    {
    }
}