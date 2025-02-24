<?php

namespace App\Entity;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\Api\User\UserDeleteController;
use App\Controller\Api\User\UserGetMeController;
use App\Controller\Api\User\UserPostController;
use App\Controller\Api\User\UserPutController;
use App\Repository\UserRepository;
use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'])]
#[GetCollection(
    uriTemplate: '/user',
    controller: UserGetMeController::class,
    paginationEnabled: false,
    normalizationContext: ['groups' => ['user:read']],
)]
#[GetCollection(
    uriTemplate: '/users',
    normalizationContext: ['groups' => ['user:read']]
)]
#[Post(
    controller: UserPostController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'username' => [
                                'type' => 'string',
                                'required' => true,
                                'example' => 'username'
                            ],
                            'password' => [
                                'type' => 'string',
                                'required' => true,
                                'example' => 'password'
                            ],
                            'passwordConfirmation' => [
                                'type' => 'string',
                                'required' => true,
                                'example' => 'password'
                            ],
                            'description' => [
                                'type' => 'string',
                                'required' => false,
                                'example' => 'About me'
                            ],
                        ]
                    ]
                ]
            ])
        )
    )
)]
#[Put(
    controller: UserPutController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'currentPassword' => [
                                'type' => 'string',
                                'required' => true,
                                'example' => 'currentPassword'
                            ],
                            'newUsername' => [
                                'type' => 'string',
                                'required' => false,
                                'example' => 'newUsername'
                            ],
                            'newPassword' => [
                                'type' => 'string',
                                'required' => false,
                                'example' => 'newPassword'
                            ],
                            'newPasswordConfirmation' => [
                                'type' => 'string',
                                'required' => false,
                                'example' => 'newPassword'
                            ],
                            'description' => [
                                'type' => 'string',
                                'required' => false,
                                'example' => 'About me'
                            ],
                        ]
                    ]
                ]
            ])
        )
    )
)]
#[Delete(
    controller: UserDeleteController::class,
    openapi: new Operation(
        requestBody: new RequestBody(
            content: new ArrayObject([
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'password' => [
                                'type' => 'string',
                                'required' => true,
                                'example' => 'password'
                            ],
                        ]
                    ]
                ]
            ])
        )
    )
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const array ROLES = [
        'Администратор' => 'ROLE_ADMIN',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['user:read'])]
    private ?string $username;

    #[ORM\Column(type: 'string', length: 2000, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $description = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    private ?string $plainPassword = null;

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }
}
