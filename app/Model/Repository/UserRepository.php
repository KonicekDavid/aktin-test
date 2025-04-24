<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\User;
use Doctrine\ORM\EntityRepository;

/** @extends EntityRepository<User> */
class UserRepository extends EntityRepository
{
}
