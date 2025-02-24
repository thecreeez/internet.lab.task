<?php

namespace App\Controller\Api\User;

use App\Controller\Api\User\input\UserDeleteRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDeleteController extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordEncoder
    )
    {
    }

    public function __invoke(#[MapRequestPayload] UserDeleteRequest $request, User $user): Response
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $request->password)) {
            return $this->json([
                'message' => 'Password wrong',
            ], 400);
        }

        $this->userRepository->remove($user, true);
        return $this->json([
            'message' => 'user deleted',
        ], Response::HTTP_OK);
    }
}