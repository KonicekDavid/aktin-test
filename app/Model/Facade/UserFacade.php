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
        $newUser = $this->prepareObject($values);

        $user = $this->em->getRepository(User::class)->findBy(['email' => $newUser->getEmail()]);
        if ($user) {
            throw new \InvalidArgumentException('User already exists.');
        }

        $this->em->persist($newUser);
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

        $role = $this->validateRole($values['role']);
        $name = $this->validateName($values['name']);
        $email = $this->validateEmail($values['email']);

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setRole($role);
        $user->setPasswordHash($passwordHash);

        return $user;
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function getById(int $id): ?User {
        return $this->em->getRepository(User::class)->find($id);
    }

    /**
     * @return array
     */
    public function getAll(): array {
        return $this->em->getRepository(User::class)->findAll();
    }

    /**
     * @param array $data
     * @return User
     */
    public function createReader(array $data): User {
        $data['role'] = UserRole::READER->value;
        return $this->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return User
     * @throws \InvalidArgumentException
     */
    public function update(int $id, array $data) {
        $user = $this->find($id);
        if (!$user) {
            throw new \InvalidArgumentException("User not found!");
        }

        if (isset($data['name'])) {
            $name = $this->validateName($data['name']);
            $user->setName($name);
        }

        if (isset($data['email'])) {
            $email = $this->validateEmail($data['email']);
            $user->setEmail($email);
        }

        if (isset($data['role'])) {
            $role = $this->validateRole($data['role']);
            $user->setRole($role);
        }

        $this->em->flush();

        return $user;
    }


    /**
     * @param int $id
     * @return void
     */
    public function remove(int $id): void {
        $user = $this->find($id);
        if (!$user) {
            throw new \InvalidArgumentException("User not found!");
        }

        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param string $rawEmail
     * @return string
     */
    private function validateEmail(string $rawEmail): string {
        $email = strtolower($rawEmail);
        if (!Validators::isEmail($email)) {
            throw new \InvalidArgumentException('Invalid email provided.');
        }
        return $email;
    }

    /**
     * @param string $rawName
     * @return string
     */
    private function validateName(string $rawName): string {
        if ($rawName === '' || strlen($rawName) === 0 || strlen($rawName) > 100) {
            throw new \InvalidArgumentException('Invalid name provided. Name must not be empty and max length is 100 characters.');
        }
        return $rawName;
    }

    /**
     * @param string $rawRole
     * @return string
     */
    private function validateRole(string $rawRole): string {
        $role = strtolower($rawRole);
        if (!UserRole::tryFrom($role)) {
            throw new \InvalidArgumentException("Role '{$role}' not found!");
        }
        return $role;
    }
}