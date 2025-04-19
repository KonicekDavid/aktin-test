<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\DTO;

enum UserRole: string
{
    case ADMIN = 'admin';
    case Author = 'author';
    case Reader = 'reader';
}