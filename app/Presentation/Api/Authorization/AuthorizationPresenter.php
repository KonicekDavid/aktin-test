<?php declare(strict_types=1);

/**
 * @author David Koníček
 */

namespace App\Presentation\Api\Authorization;
use Nette\Application\Attributes\Requires;
use Nette\Application\UI\Presenter;

class AuthorizationPresenter extends Presenter {

    #[Requires(methods: ['POST'], forward: false)]
    public function actionRegister() {

    }

    #[Requires(methods: ['POST'], forward: false)]
    public function actionLogin() {

    }
}