<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Entity;

use App\DTO\UserRole;
use App\Model\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Validators;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User {

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    public int $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', unique: true)]
    public string $email;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private string $passwordHash;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    public string $name;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    public string $role;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Article::class)]
    private Collection $articles;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }

    /**
     * @return Collection
     */
    public function getArticles(): Collection {
        return $this->articles;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void {
        $this->email = $this->validateEmail($email);
    }

    /**
     * @param string $passwordHash
     * @return void
     */
    public function setPasswordHash(string $passwordHash): void {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void {
        $this->name = $this->validateName($name);
    }

    /**
     * @param string $role
     * @return void
     */
    public function setRole(string $role): void {
        $this->role = $this->validateRole($role);
    }

    /**
     * @param string $rawEmail
     * @return string
     */
    private function validateEmail(string $rawEmail): string {
        $email = strtolower($rawEmail);
        if (!Validators::isEmail($email)) {
            throw new \InvalidArgumentException('Invalid email provided.');
        }
        return $email;
    }

    /**
     * @param string $rawName
     * @return string
     */
    private function validateName(string $rawName): string {
        if ($rawName === '' || strlen($rawName) === 0 || strlen($rawName) > 100) {
            throw new \InvalidArgumentException('Invalid name provided. Name must not be empty and max length is 100 characters.');
        }
        return $rawName;
    }

    /**
     * @param string $rawRole
     * @return string
     */
    private function validateRole(string $rawRole): string {
        $role = strtolower($rawRole);
        if (!UserRole::tryFrom($role)) {
            throw new \InvalidArgumentException("Role '{$role}' not found!");
        }
        return $role;
    }
}