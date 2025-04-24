<?php

declare(strict_types=1);

namespace App\Model\Facade;

use App\DTO\UserRole;
use App\Model\Entity\Article;
use App\Model\Entity\User;
use App\Security\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\Passwords;

class UserFacade
{
    public function __construct(
        private EntityManagerInterface $em,
        private Passwords $passwords,
        private JWTService $jwtService
    ) {
    }

    /**
     * @param array $values
     * @return User
     * @throws \InvalidArgumentException
     */
    public function create(array $values): User
    {
        $newUser = $this->prepareObject($values);

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $newUser->getEmail()]);
        if ($user) {
            throw new \InvalidArgumentException('User already exists.');
        }

        $this->em->persist($newUser);
        $this->em->flush();
        return $newUser;
    }

    /**
     * @param array $data
     * @return string|null
     */
    public function login(array $data): ?string
    {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return null;
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => strtolower($email)]);
        if (!$user || !$this->passwords->verify($password, $user->getPasswordHash())) {
            return null;
        }

        return $this->jwtService->generateToken($user);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User
    {
        return $this->em->getRepository(User::class)->find($id);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function getById(int $id): ?User
    {
        return $this->em->getRepository(User::class)->find($id);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->em->getRepository(User::class)->findAll();
    }

    /**
     * @param array $data
     * @return User
     */
    public function createReader(array $data): User
    {
        $data['role'] = UserRole::READER->value;
        return $this->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return User
     * @throws \InvalidArgumentException
     */
    public function update(int $id, array $data)
    {
        $user = $this->find($id);
        if (!$user) {
            throw new \InvalidArgumentException("User not found!");
        }

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['role'])) {
            $user->setRole($data['role']);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }


    /**
     * @param int $id
     * @return void
     */
    public function remove(int $id): void
    {
        $user = $this->find($id);
        if (!$user) {
            throw new \InvalidArgumentException("User not found!");
        }

        if (count($this->em->getRepository(Article::class)->findBy(['author' => $user]))) {
            throw new \InvalidArgumentException("Cannot delete user with articles!");
        }

        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param string $email
     * @return User
     * @throws \ErrorException
     */
    public function getByEmail(string $email): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new \ErrorException("User not found!");
        }
        return $user;
    }

    /**
     * @param array $values
     * @return User
     * @throws \InvalidArgumentException
     */
    private function prepareObject(array $values): User
    {
        if (strlen($values['password']) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }
        $passwordHash = $this->passwords->hash($values['password']);

        $user = new User();
        $user->setName($values['name']);
        $user->setEmail($values['email']);
        $user->setRole($values['role']);
        $user->setPasswordHash($passwordHash);

        return $user;
    }
}
