<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Facade;

use App\DTO\UserRole;
use App\Model\Entity\User;
use App\Security\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\Passwords;
use Nette\Utils\Validators;

class UserFacade {
    public function __construct(
        private EntityManagerInterface $em,
        private Passwords              $passwords,
        private JWTService             $jwtService
    ) {
    }

    /**
     * @param array $values
     * @return User
     * @throws \InvalidArgumentException
     */
    public function create(array $values): User {
        print_r($this->em->getRepository(User::class)->findAll(), true);
        die('test');

        $user = $this->prepareObject($values);

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function login(array $data): ?string {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return null;
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user || !$this->passwords->verify($password, $user->getPasswordHash())) {
            return null;
        }

        return $this->jwtService->generateToken($user);
    }

    public function find(int $id): ?User {
        return $this->em->getRepository(User::class)->find($id);
    }

    /**
     * @param array $values
     * @return User
     * @throws \InvalidArgumentException
     */
    private function prepareObject(array $values): User {
        if (strlen($values['password']) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }
        $passwordHash = $this->passwords->hash($values['password']);

        $role = strtolower($values['role']);
        if (!UserRole::tryFrom($role)) {
            throw new \InvalidArgumentException("Role '{$role}' not found!");
        }

        $email = strtolower($values['email']);
        if (!Validators::isEmail($email)) {
            throw new \InvalidArgumentException('Invalid email provided.');
        }

        $user = new User();
        $user->setName($values['name']);
        $user->setEmail($email);
        $user->setRole($role);
        $user->setPasswordHash($passwordHash);

        return $user;
    }
}