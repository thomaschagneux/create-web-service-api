<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationQuery
{
    #[Assert\NotBlank(message: 'La page est obligatoire')]
    #[Assert\Type(type: 'digit', message: 'La page doit être un nombre')]
    #[Assert\GreaterThan(value: 0, message: 'La page doit être supérieure à 0')]
    private string $page;

    #[Assert\NotBlank(message: 'La limite est obligatoire')]
    #[Assert\Type(type: 'digit', message: 'La limite doit être un nombre')]
    #[Assert\GreaterThan(value: 0, message: 'La limite doit être supérieure à 0')]
    private string $limit;

    public function __construct(string $page = '1', string $limit = '20')
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function setPage(string $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getLimit(): string
    {
        return $this->limit;
    }

    public function setLimit(string $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getPageAsInt(): int
    {
        return (int) $this->page;
    }

    public function getLimitAsInt(): int
    {
        return (int) $this->limit;
    }
}
