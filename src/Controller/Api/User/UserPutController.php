<?php

namespace App\Controller\Api\User;

use App\Controller\Api\User\input\UserPutRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPutController extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordEncoder
    )
    {
    }

    public function __invoke(#[MapRequestPayload] UserPutRequest $request, User $user): Response
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $request->currentPassword)) {
            return $this->json([
                'message' => 'Password wrong.',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($request->newPassword) {
            if ($request->newPassword !== $request->newPasswordConfirmation) {
                return $this->json([
                    'message' => 'newPassword and newPassword confirmation do not match.',
                ], Response::HTTP_BAD_REQUEST);
            }

            $user->setPassword($this->passwordEncoder->hashPassword($user, $request->newPassword));
        }

        if ($request->description) {
            $user->setDescription($request->description);
        }

        if ($request->newUsername) {
            if ($user->getUsername() === $request->newUsername) {
                return $this->json([
                    'message' => 'You already have this username.',
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($this->userRepository->findOneBy(['username' => $request->newUsername])) {
                return $this->json([
                    'message' => 'User with username ' . $request->newUsername . ' already exist.',
                ], Response::HTTP_BAD_REQUEST);
            }

            $user->setUsername($request->newUsername);
        }

        $this->userRepository->save($user, true);
        return $this->json($user, Response::HTTP_OK, [], ['groups' => ['user:read']]);
    }
}