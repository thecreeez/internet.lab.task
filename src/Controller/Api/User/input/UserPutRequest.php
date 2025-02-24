<?php

namespace App\Controller\Api\User\input;

class UserPutRequest
{
    public function __construct(
        public string  $currentPassword,
        public ?string $newUsername = null,
        public ?string $newPassword = null,
        public ?string $newPasswordConfirmation = null,
        public ?string $description = null,
    )
    {
    }
}