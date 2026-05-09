<?php

namespace App\Entity;

use App\Repository\ComplaintRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ComplaintRepository::class)]
class Complaint
{
    public const STATUS_NEW = 'NEW';
    public const STATUS_IN_REVIEW = 'IN_REVIEW';
    public const STATUS_RESOLVED = 'RESOLVED';
    public const STATUS_REJECTED = 'REJECTED';

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_REVIEW,
        self::STATUS_RESOLVED,
        self::STATUS_REJECTED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_NEW;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'complaints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserEmailNo $createdBy = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = self::STATUS_NEW;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): ?UserEmailNo
    {
        return $this->createdBy;
    }

    public function setCreatedBy(UserEmailNo $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
