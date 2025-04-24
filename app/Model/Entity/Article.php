<?php

declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'article')]
class Article
{
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
    #[ORM\Column(type: 'string')]
    public string $title;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    public string $content;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    public User $author;

    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: 'datetime_immutable')]
    public DateTimeImmutable $updatedAt;

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $this->validateTitle($title);
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return void
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     * @return void
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param User $author
     * @return void
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @param string $title
     * @return string
     */
    private function validateTitle(string $title): string
    {
        if (empty($title) || strlen($title) < 1) {
            throw new \InvalidArgumentException('Title cannot be empty');
        }
        return $title;
    }
}
