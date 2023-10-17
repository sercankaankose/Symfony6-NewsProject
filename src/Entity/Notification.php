<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_at = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?News $news_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $notifications = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDateAt(): ?\DateTimeInterface
    {
        return $this->date_at;
    }

    public function setDateAt(?\DateTimeInterface $date_at): static
    {
        $this->date_at = $date_at;

        return $this;
    }

    public function getNewsId(): ?News
    {
        return $this->news_id;
    }

    public function setNewsId(?News $news_id): static
    {
        $this->news_id = $news_id;

        return $this;
    }

    public function getNotifications(): ?int
    {
        return $this->notifications;
    }

    public function setNotifications(?int $notifications): static
    {
        $this->notifications = $notifications;

        return $this;
    }
}
