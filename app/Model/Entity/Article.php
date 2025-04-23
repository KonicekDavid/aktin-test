<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Entity;

use App\Model\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'article')]
class Article {

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private string $title;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    private string $content;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $author;

    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;
}