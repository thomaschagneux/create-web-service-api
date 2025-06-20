<?php

namespace App\Dto;

use JMS\Serializer\Annotation\SerializedName;

class BuyerData
{
    #[SerializedName('first_name')]
    private string $firstName;

    #[SerializedName('last_name')]
    private string $lastName;

    #[SerializedName('customerId')]
    private int $apiUserId;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getApiUserId(): int
    {
        return $this->apiUserId;
    }

    public function setApiUserId(int $apiUserId): void
    {
        $this->apiUserId = $apiUserId;
    }
}
