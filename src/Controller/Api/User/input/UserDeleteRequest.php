<?php

namespace App\Controller\Api\User\input;

class UserDeleteRequest
{
    public function __construct(
        public string $password,
    )
    {
    }
}