<?php

namespace App\Entity;

use App\Repository\BuyerRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BuyerRepository::class)]
class Buyer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getBuyerList', 'getBuyer'])]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\ManyToOne(inversedBy: 'buyers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['getBuyerList', 'getBuyer'])]
    private ApiUser $apiUser;

    #[Groups(['getBuyerList', 'getBuyer'])]
    #[ORM\Column(length: 255)]
    private string $firstName;

    #[Groups(['getBuyerList', 'getBuyer'])]
    #[ORM\Column(length: 255)]
    private string $lastName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getApiUser(): ApiUser
    {
        return $this->apiUser;
    }

    public function setApiUser(ApiUser $apiUser): void
    {
        $this->apiUser = $apiUser;
    }
}
