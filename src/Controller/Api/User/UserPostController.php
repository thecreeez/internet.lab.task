<?php

namespace App\Controller\Api\User;

use App\Controller\Api\User\input\UserPostRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPostController extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordEncoder
    )
    {
    }

    public function __invoke(#[MapRequestPayload] UserPostRequest $request): Response
    {
        if ($this->userRepository->findOneBy(['username' => $request->username])) {
            return $this->json([
                'message' => 'Username already taken',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($request->password !== $request->passwordConfirmation) {
            return $this->json([
                'message' => 'password and passwordConfirmation do not match',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setUsername($request->username);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $request->password));
        $user->setDescription($request->description);

        $this->userRepository->save($user, true);
        return $this->json($user, Response::HTTP_OK, [], ['groups' => ['user:read']]);
    }
}