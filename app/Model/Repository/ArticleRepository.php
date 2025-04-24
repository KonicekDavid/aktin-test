<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;
use Doctrine\ORM\EntityRepository;

/** @extends EntityRepository<Article> */
class ArticleRepository extends EntityRepository
{
}
