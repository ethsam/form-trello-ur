<?php

namespace App\Entity;

use App\Repository\ActionColumRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionColumRepository::class)]
class ActionColum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $emailReceipt = null;

    #[ORM\Column(length: 255)]
    private ?string $titleColumn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idColumn = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column]
    private ?bool $mailForSender = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEmailReceipt(): ?string
    {
        return $this->emailReceipt;
    }

    public function setEmailReceipt(string $emailReceipt): static
    {
        $this->emailReceipt = $emailReceipt;

        return $this;
    }

    public function getTitleColumn(): ?string
    {
        return $this->titleColumn;
    }

    public function setTitleColumn(string $titleColumn): static
    {
        $this->titleColumn = $titleColumn;

        return $this;
    }

    public function getIdColumn(): ?string
    {
        return $this->idColumn;
    }

    public function setIdColumn(?string $idColumn): static
    {
        $this->idColumn = $idColumn;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isMailForSender(): ?bool
    {
        return $this->mailForSender;
    }

    public function setMailForSender(bool $mailForSender): static
    {
        $this->mailForSender = $mailForSender;

        return $this;
    }
}
