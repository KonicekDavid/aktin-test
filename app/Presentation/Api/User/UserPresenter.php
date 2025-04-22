<?php declare(strict_types=1);

/**
 * @author David Koníček
 */

namespace App\Presentation\Api\User;

use Nette\Application\Attributes\Requires;
use Nette\Application\UI\Presenter;

class UserPresenter extends Presenter {

    #[Requires(methods: ['GET', 'POST', 'PUT', 'DELETE'], forward: false)]
    public function actionDefault(?int $id): void {
        $this->sendJson('Users OK');
    }
}