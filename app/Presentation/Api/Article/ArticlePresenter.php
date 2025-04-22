<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Presentation\Api\Article;

use Nette\Application\Attributes\Requires;
use Nette\Application\UI\Presenter;

class ArticlePresenter extends Presenter {

    #[Requires(methods: ['GET', 'POST', 'PUT', 'DELETE'], forward: false, actions: 'default')]
    public function actionDefault(?int $id): void {
        $this->sendJson('Articles OK');
    }
}