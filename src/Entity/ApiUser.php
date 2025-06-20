<?php

namespace App\Entity;

use App\Repository\ApiUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Hateoas\Relation(
    'self',
    href: new Hateoas\Route(
        'show_buyer',
        parameters: [
            'id' => 'expr(object.getId())',
        ]
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getBuyerList', 'getBuyer'])
)]
#[Hateoas\Relation(
    'list',
    href: new Hateoas\Route(
        'get_buyers',
        parameters: [
        ]
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getBuyerList', 'getBuyer'])
)]
#[Hateoas\Relation(
    'delete',
    href: new Hateoas\Route(
        'delete_buyer',
        parameters: [
            'id' => 'expr(object.getId())',
        ]
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getBuyerList', 'getBuyer'])
)]
#[ORM\Entity(repositoryClass: ApiUserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class ApiUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getBuyerList', 'getBuyer'])]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\Column(length: 180)]
    #[Groups(['getBuyerList', 'getBuyer'])]
    private string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['getBuyerList', 'getBuyer'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    /**
     * @var Collection<int, Buyer>
     */
    #[ORM\OneToMany(targetEntity: Buyer::class, mappedBy: 'apiUser', orphanRemoval: true)]
    private Collection $buyers;

    public function __construct()
    {
        $this->buyers = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Buyer>
     */
    public function getBuyers(): Collection
    {
        return $this->buyers;
    }

    public function addBuyer(Buyer $buyer): static
    {
        if (!$this->buyers->contains($buyer)) {
            $this->buyers->add($buyer);
            $buyer->setApiUser($this);
        }

        return $this;
    }

    public function removeBuyer(Buyer $buyer): static
    {
        $this->buyers->removeElement($buyer);

        return $this;
    }
}
