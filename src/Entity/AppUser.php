<?php

namespace App\Entity;

use App\Repository\AppUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Hateoas\Relation(
    'self',
    href: new Hateoas\Route(
        'show_user',
        parameters: [
            'id' => 'expr(object.getId())',
            'customer_id' => 'expr(object.getCustomer().getId())',
        ]
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getUserList', 'getUser'])
)]
#[Hateoas\Relation(
    'list',
    href: new Hateoas\Route(
        'list_user_by_customer',
        parameters: [
            'id' => 'expr(object.getCustomer().getId())',
        ]
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getUserList', 'getUser'])
)]
#[Hateoas\Relation(
    'delete',
    href: new Hateoas\Route(
        'delete_user',
        parameters: [
            'id' => 'expr(object.getId())',
            'customer_id' => 'expr(object.getCustomer().getId())',
        ]
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getUserList', 'getUser'])
)]
#[ORM\Entity(repositoryClass: AppUserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class AppUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\Column(length: 180)]
    #[Groups(['getUserList', 'getUser'])]
    private string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['getUserList', 'getUser'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['getUserList', 'getUser'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['getUserList', 'getUser'])]
    private ?string $lastName = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['getUserList', 'getUser'])]
    private Customer $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Function for JWT.
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email; // @phpstan-ignore-line
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

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
