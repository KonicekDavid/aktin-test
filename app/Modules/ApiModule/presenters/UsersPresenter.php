<?php declare(strict_types=1);

/**
 * @author David Koníček
 */

namespace App\ApiModule\Presenters;

use Nette\Application\UI\Presenter;

class UsersPresenter extends Presenter {

    public function actionDefault(?int $id): void {
        $this->sendJson('Users OK');
    }
}