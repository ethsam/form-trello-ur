<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateEvent = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateLimit = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $brief = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $composante = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $link = null;

    /**
     * @var Collection<int, Attachment>
     */
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Attachment::class, cascade: ['persist', 'remove'], orphanRemoval: false)]
    private Collection $attachments;

    /**
     * @var Collection<int, Format>
     */
    #[ORM\ManyToMany(targetEntity: Format::class, mappedBy: 'tickets')]
    private Collection $formats;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->formats = new ArrayCollection();
    }

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDateEvent(): ?\DateTimeImmutable
    {
        return $this->dateEvent;
    }

    public function setDateEvent(?\DateTimeImmutable $dateEvent): static
    {
        $this->dateEvent = $dateEvent;

        return $this;
    }

    public function getDateLimit(): ?\DateTimeImmutable
    {
        return $this->dateLimit;
    }

    public function setDateLimit(?\DateTimeImmutable $dateLimit): static
    {
        $this->dateLimit = $dateLimit;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBrief(): ?string
    {
        return $this->brief;
    }

    public function setBrief(string $brief): static
    {
        $this->brief = $brief;

        return $this;
    }

    public function getComposante(): ?string
    {
        return $this->composante;
    }

    public function setComposante(?string $composante): static
    {
        $this->composante = $composante;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): static
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setTicket($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): static
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getTicket() === $this) {
                $attachment->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Format>
     */
    public function getFormats(): Collection
    {
        return $this->formats;
    }

    public function addFormat(Format $format): static
    {
        if (!$this->formats->contains($format)) {
            $this->formats->add($format);
            $format->addTicket($this);
        }

        return $this;
    }

    public function removeFormat(Format $format): static
    {
        if ($this->formats->removeElement($format)) {
            $format->removeTicket($this);
        }

        return $this;
    }
}
