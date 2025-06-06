<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getUserList', 'getUser'])]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\Column(length: 255)]
    #[Groups(['getUserList', 'getUser'])]
    private string $name;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['getUserList', 'getUser'])]
    private string $apiKey;

    /**
     * @var Collection<int, AppUser>
     */
    #[ORM\OneToMany(targetEntity: AppUser::class, mappedBy: 'customer')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return Collection<int, AppUser>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(AppUser $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCustomer($this);
        }

        return $this;
    }

    public function removeUser(AppUser $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }
}
