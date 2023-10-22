<?php

namespace App\Entity;


use App\Repository\UserRepository;
use App\Validator\Constraints\PasswordPolicy;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements PasswordAuthenticatedUserInterface, UserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    private Collection $userRoles;


    #[ORM\OneToMany(targetEntity: News::class, mappedBy:'editor')]
    private Collection $editorNews;

    #[ORM\OneToMany(targetEntity: News::class, mappedBy:'author')]
    private Collection $authoredNews;


    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Notification::class)]
    private Collection $notifications;


//    #[ORM\OneToMany(mappedBy: 'editor', targetEntity: Notification::class)]
//    private Collection $notifyeditor;
//
//    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Notification::class)]
//    private Collection $NotifyAuthor;


    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->news = new ArrayCollection();
//        $this->notifyeditor = new ArrayCollection();
//        $this->NotifyAuthor = new ArrayCollection();
$this->notifications = new ArrayCollection();
    }

    public function getNews(): ArrayCollection
    {
        return $this->news;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->userRoles as $role) {
            $roles[] = $role->getRoleName();

        }

        return $roles;
    }

    public function addRoles(Role $role): static
    {
        if (!$this->userRoles->contains($role)) {
            $this->userRoles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        $this->userRoles->removeElement($role);

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }


    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

//    /**
//     * @return Collection<int, Notification>
//     */
//    public function getNotifyeditor(): Collection
//    {
//        return $this->notifyeditor;
//    }
//
//    public function addNotifyeditor(Notification $notifyeditor): static
//    {
//        if (!$this->notifyeditor->contains($notifyeditor)) {
//            $this->notifyeditor->add($notifyeditor);
//            $notifyeditor->setEditor($this);
//        }
//
//        return $this;
//    }
//
//    public function removeNotifyeditor(Notification $notifyeditor): static
//    {
//        if ($this->notifyeditor->removeElement($notifyeditor)) {
//            // set the owning side to null (unless already changed)
//            if ($notifyeditor->getEditor() === $this) {
//                $notifyeditor->setEditor(null);
//            }
//        }
//
//        return $this;
//    }
//
//    /**
//     * @return Collection<int, Notification>
//     */
//    public function getNotifyAuthor(): Collection
//    {
//        return $this->NotifyAuthor;
//    }
//
//    public function addNotifyAuthor(Notification $notifyAuthor): static
//    {
//        if (!$this->NotifyAuthor->contains($notifyAuthor)) {
//            $this->NotifyAuthor->add($notifyAuthor);
//            $notifyAuthor->setAuthor($this);
//        }
//
//        return $this;
//    }
//
//    public function removeNotifyAuthor(Notification $notifyAuthor): static
//    {
//        if ($this->NotifyAuthor->removeElement($notifyAuthor)) {
//            // set the owning side to null (unless already changed)
//            if ($notifyAuthor->getAuthor() === $this) {
//                $notifyAuthor->setAuthor(null);
//            }
//        }
//
//        return $this;
//    }
//

/**
 * @return Collection<int, Notification>
 */
public function getNotifications(): Collection
{
    return $this->notifications;
}

public function addNotification(Notification $notification): static
{
    if (!$this->notifications->contains($notification)) {
        $this->notifications->add($notification);
        $notification->setPerson($this);
    }

    return $this;
}

public function removeNotification(Notification $notification): static
{
    if ($this->notifications->removeElement($notification)) {
        // set the owning side to null (unless already changed)
        if ($notification->getPerson() === $this) {
            $notification->setPerson(null);
        }
    }

    return $this;
}


}
