<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Presentation\Api;

use App\DTO\UserRole;
use App\Security\JWTService;
use Nette\Application\UI\Presenter;

abstract class BaseApiPresenter extends Presenter {

    /** @var JWTService $jwtService @inject */
    public JWTService $jwtService;

    /** @var UserRole $userRole */
    protected UserRole $userRole;

    public function __construct(JWTService $jwtService)
    {
        parent::__construct();
        $this->jwtService = $jwtService;
    }

    protected function startup(): void
    {
        parent::startup();

        $authHeader = $this->getHttpRequest()->getHeader('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            $this->error('Forbidden', 403);
        }

        $token = substr($authHeader, 7);
        try {
            $payload = $this->jwtService->validate($token);
            $this->userRole = UserRole::from($payload['role']);
        } catch (\Throwable $e) {
            $this->error('Forbidden', 403);
        }
    }

    protected function requireRole(UserRole|array $roles): void
    {
        if (is_array($roles)) {
            if (!in_array($this->userRole, $roles, true)) {
                $this->error('Access denied', 403);
            }
        } else {
            if ($this->userRole !== $roles) {
                $this->error('Access denied', 403);
            }
        }
    }
}