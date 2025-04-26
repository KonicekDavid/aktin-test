<?php

declare(strict_types=1);

namespace App\Presentation\Api\Authorization;

use App\Model\Facade\UserFacade;
use Nette\Application\Attributes\Requires;
use Nette\Application\UI\Presenter;
use Nette\Http\Response;
use Tracy\Debugger;

class AuthorizationPresenter extends Presenter
{
    /** @var UserFacade $userFacade @inject */
    public UserFacade $userFacade;

    /**
     * @return void
     */
    #[Requires(methods: ['POST'], forward: false)]
    public function actionRegister()
    {
        $data = json_decode($this->getHttpRequest()->getRawBody() ?? '', true) ?? [];
        $response = $this->getHttpResponse();
        try {
            $this->userFacade->create($data);
            $response->setCode(Response::S201_Created);
        } catch (\InvalidArgumentException $e) {
            $message = $e->getMessage();
            $response->setCode(Response::S400_BadRequest, $message);
        } catch (\Throwable $exception) {
            Debugger::log($exception->getMessage(), Debugger::ERROR);
            $message = 'Application error';
            $response->setCode(Response::S500_InternalServerError, $message);
        }
        $this->terminate();
    }

    /**
     * @return void
     */
    #[Requires(methods: ['POST'], forward: false)]
    public function actionLogin()
    {
        $data = json_decode($this->getHttpRequest()->getRawBody() ?? '', true) ?? [];
        $response = $this->getHttpResponse();
        $token = null;
        try {
            $token = $this->userFacade->login($data);
            if ($token) {
                $response->setCode(Response::S200_OK);
            } else {
                $response->setCode(Response::S400_BadRequest, 'Invalid credentials');
            }
        } catch (\Throwable $exception) {
            $response->setCode(Response::S500_InternalServerError);
        }
        if ($token) {
            $this->sendJson(['token' => $token]);
        }
        $this->terminate();
    }
}
