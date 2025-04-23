<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Repository;

use App\Model\Entity\Article;
use Doctrine\ORM\EntityRepository;

/** @extends EntityRepository<Article> */
class ArticleRepository extends EntityRepository {

}