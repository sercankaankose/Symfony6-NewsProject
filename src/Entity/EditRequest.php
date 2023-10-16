<?php

namespace App\Entity;

use App\Repository\EditRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EditRequestRepository::class)]
class EditRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(targetEntity: News::class, inversedBy: 'editRequests')]
    #[ORM\JoinColumn(name: 'news_id', referencedColumnName: 'id', nullable: false)]
    private ?News $news;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'editorNews')]
    #[ORM\JoinColumn(nullable: true, name: 'editor_id')]
    private ?User $editor = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $editorNote = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $request_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $accepted_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): self
    {
        $this->news = $news;

        return $this;
    }

//    public function setNewsId(?int $newsId): self
//    {
//        $this->newsId = $newsId;
//
//        return $this;
//    }


    public function getEditor(): ?User
    {
        return $this->editor;
    }

    public function setEditor(?User $editor): void
    {
        $this->editor = $editor;
    }

    public function getEditorNote(): ?string
    {
        return $this->editorNote;
    }

    public function setEditorNote(?string $editorNote): void
    {
        $this->editorNote = $editorNote;
    }

    public function getRequestAt(): ?\DateTimeInterface
    {
        return $this->request_at;
    }

    public function setRequestAt(?\DateTimeInterface $request_at): void
    {
        $this->request_at = $request_at;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
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

    public function getAcceptedAt(): ?\DateTimeInterface
    {
        return $this->accepted_at;
    }

    public function setAcceptedAt(?\DateTimeInterface $accepted_at): static
    {
        $this->accepted_at = $accepted_at;

        return $this;
    }



}
