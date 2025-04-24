<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Facade;

use App\Model\Entity\Article;
use App\Model\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ArticleFacade
{
    public function __construct(private EntityManagerInterface $em) {
    }

    public function create(array $data, User $user): Article {
        $now = new \DateTimeImmutable();

        $article = new Article();
        $article->setTitle($data['title'] ?? '');
        $article->setContent($data['content'] ?? '');
        $article->setCreatedAt($now);
        $article->setUpdatedAt($now);
        $article->setAuthor($user);

        $this->em->persist($article);
        $this->em->flush();
        return $article;
    }

    public function getAll(): array {
        return $this->em->getRepository(Article::class)->findAll();
    }

    public function getById(int $id): ?Article {
        return $this->em->getRepository(Article::class)->find($id);
    }

    public function update(Article $article, array $data): Article
    {
        if (isset($data['title'])) {
            $article->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $article->setTitle($data['content']);
        }

        $article->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($article);
        $this->em->flush();

        return $article;
    }

    public function remove(Article $article): void {
        $this->em->remove($article);
        $this->em->flush();
    }
}