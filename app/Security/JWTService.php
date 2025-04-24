<?php

declare(strict_types=1);

namespace App\Security;

use Ahc\Jwt\JWT;
use App\Model\Entity\User;

class JWTService
{
    /**
     * @var JWT
     */
    private JWT $jwt;

    public function __construct(string $secret, int $ttl)
    {
        $this->jwt = new JWT($secret, 'HS256', $ttl);
    }

    /**
     * @param User $user
     * @return string
     */
    public function generateToken(User $user): string
    {
        return $this->jwt->encode([
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'role'  => $user->getRole()
        ]);
    }

    /**
     * @param string $token
     * @return array
     */
    public function validate(string $token): array
    {
        return $this->jwt->decode($token);
    }
}
