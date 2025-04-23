<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Facade;

use App\Model\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class ArticleFacade {
    public function __construct(private EntityManagerInterface $em) {
    }

    public function create(Article $article): Article {
        $this->em->persist($article);
        $this->em->flush();
        return $article;
    }

    public function getArticleById(int $id): Article {
        return $this->em->getRepository(Article::class)->find($id);
    }
}