<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class User implements UserInterface
{
    public const PROFILE_LANGUAGE_EN = 'en';
    public const PROFILE_LANGUAGE_RU = 'ru';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email = '';

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The plain password before update
     */
    private ?string $plainPassword = '';

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password = '';

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $is_email_verified = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $profile_language = self::PROFILE_LANGUAGE_EN;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $is_blocked_by_admin = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $is_deleted_by_user = false;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $created_at = null;

    /**
     * @var DateTime $updated_at
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $updated_at = null;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function __toString()
    {
        return (string) $this->email;
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): string
    {
        return (string) $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getIsEmailVerified(): bool
    {
        return $this->is_email_verified;
    }

    public function setIsEmailVerified(bool $is_email_verified): self
    {
        $this->is_email_verified = $is_email_verified;

        return $this;
    }

    public function getProfileLanguage(): string
    {
        return $this->profile_language;
    }

    public function setProfileLanguage(string $profile_language): self
    {
        $this->profile_language = $profile_language;

        return $this;
    }

    public function getIsBlockedByAdmin(): bool
    {
        return $this->is_blocked_by_admin;
    }

    public function setIsBlockedByAdmin(bool $is_blocked_by_admin): self
    {
        $this->is_blocked_by_admin = $is_blocked_by_admin;

        return $this;
    }

    public function getIsDeletedByUser(): bool
    {
        return $this->is_deleted_by_user;
    }

    public function setIsDeletedByUser(bool $is_deleted_by_user): self
    {
        $this->is_deleted_by_user = $is_deleted_by_user;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bookcase", mappedBy="user", cascade={"persist", "remove"})
     */
    private Bookcase $bookcase;

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = '';
    }

    public function getBookcase(): Bookcase
    {
        return $this->bookcase;
    }

    public function setBookcase(Bookcase $bookcase): self
    {
        $this->bookcase = $bookcase;

        // set the owning side of the relation if necessary
        if ($bookcase->getUser() !== $this) {
            $bookcase->setUser($this);
        }

        return $this;
    }

    /**
     * @ORM\PostPersist
     * @param LifecycleEventArgs $args
     */
    public function onCreateBindWithBookcase(LifecycleEventArgs $args)
    {
        $entityManager = $args->getObjectManager();
        $bookcase = new Bookcase($this);
        $entityManager->persist($bookcase);
        $entityManager->flush();
    }
}
