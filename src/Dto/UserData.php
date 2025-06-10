<?php

namespace App\Dto;

use JMS\Serializer\Annotation\SerializedName;

class UserData
{
    #[SerializedName('email')]
    private string $email;

    #[SerializedName('password')]
    private string $password;

    #[SerializedName('first_name')]
    private string $firstName;

    #[SerializedName('last_name')]
    private string $lastName;

    #[SerializedName('role')]
    private string $role;

    #[SerializedName('customerId')]
    private int $customerId;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }
}
