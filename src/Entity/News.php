<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'authoredNews')]
    #[ORM\JoinColumn(nullable: false, name: 'author_id')]
    private User $author;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'editorNews')]
    #[ORM\JoinColumn(nullable: true, name: 'editor_id')]
    private ?User $editor = null;


    #[ORM\Column(length: 255)]
    private ?string $title = null;

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?int $view_count = null;

    #[ORM\ManyToOne(inversedBy: 'content')]
    private ?Content $category = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(targetEntity: EditRequest::class, mappedBy: 'news')]
    private Collection $editRequests;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $published_at = null;

    #[ORM\OneToMany(mappedBy: 'news_id', targetEntity: Notification::class)]
    private Collection $notifications;



    public function __construct()
    {
        $this->editRequests = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorId(): ?User
    {
        return $this->authorId;
    }

    public function setAuthorId(?User $authorId): static
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function getEditor(): ?User
    {
        return $this->editor;
    }

    public function setEditor(?User $editor): void
    {
        $this->editor = $editor;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
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

    public function getViewCount(): ?int
    {
        return $this->view_count;
    }

    public function setViewCount(?int $view_count): static
    {
        $this->view_count = $view_count;

        return $this;
    }

    public function getCategory(): ?Content
    {
        return $this->category;
    }

    public function setCategory(?Content $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }


    public function setId(?int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return Collection<int, EditRequest>
     */
    public function getEditRequests(): Collection
    {
        return $this->editRequests;
    }

    public function addEditRequest(EditRequest $editRequest): self
    {
        if (!$this->editRequests->contains($editRequest)) {
            $this->editRequests[] = $editRequest;
            $editRequest->setNews($this);
        }

        return $this;
    }

    public function removeEditRequest(EditRequest $editRequest): self
    {
        if ($this->editRequests->removeElement($editRequest)) {
            // set the owning side to null (unless already changed)
            if ($editRequest->getNews() === $this) {
                $editRequest->setNews(null);
            }
        }

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->published_at;
    }

    public function setPublishedAt(?\DateTimeInterface $published_at): static
    {
        $this->published_at = $published_at;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotifications(Notification $notifications): static
    {
        if (!$this->notifications->contains($notifications)) {
            $this->notifications->add($notifications);
            $notifications->setNewsId($this);
        }

        return $this;
    }

    public function removeNotifications(Notification $notifications): static
    {
        if ($this->notifications->removeElement($notifications)) {
            // set the owning side to null (unless already changed)
            if ($notifications->getNewsId() === $this) {
                $notifications->setNewsId(null);
            }
        }

        return $this;
    }



}
