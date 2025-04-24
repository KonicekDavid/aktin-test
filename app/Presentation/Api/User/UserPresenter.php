<?php declare(strict_types=1);

/**
 * @author David Koníček
 */

namespace App\Presentation\Api\User;

use App\DTO\UserRole;
use App\Model\Facade\UserFacade;
use App\Presentation\Api\BaseApiPresenter;
use Nette\Application\Attributes\Requires;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Tracy\Debugger;

class UserPresenter extends BaseApiPresenter {

    /** @var UserFacade $userFacade @inject */
    public UserFacade $userFacade;

    #[Requires(methods: ['GET', 'POST', 'PUT', 'DELETE'], forward: false)]
    public function actionDefault(?int $id): void {

        $this->requireRole(UserRole::ADMIN);

        $response = $this->getHttpResponse();

        switch ($this->getHttpRequest()->getMethod()) {
            case 'GET':
                if ($id !== null) {
                    $user = $this->userFacade->getById($id);
                    $user ? $this->sendJson($user) : $this->error('User not found', IResponse::S204_NoContent);
                } else {
                    $this->sendJson($this->userFacade->getAll());
                }
                break;
            case 'POST':
                $data = json_decode($this->getHttpRequest()->getRawBody() ?? '', true) ?? [];
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
                break;
            case 'PUT':
                $data = json_decode($this->getHttpRequest()->getRawBody() ?? '', true) ?? [];
                try {
                    if ($id !== null) {
                        $this->userFacade->update($id, $data);
                        $response->setCode(Response::S200_OK);
                    } else {
                        $response->setCode(Response::S400_BadRequest, 'Specified id is missing in url');
                    }
                } catch (\InvalidArgumentException $e) {
                    $message = $e->getMessage();
                    $response->setCode(Response::S400_BadRequest, $message);
                } catch (\Throwable $exception) {
                    Debugger::log($exception->getMessage(), Debugger::ERROR);
                    $message = 'Application error';
                    $response->setCode(Response::S500_InternalServerError, $message);
                }
                break;
            case 'DELETE':
                try {
                    if ($id !== null) {
                        $this->userFacade->remove($id);
                        $response->setCode(Response::S200_OK);
                    } else {
                        $response->setCode(Response::S400_BadRequest, 'Specified id is missing in url');
                    }
                } catch (\InvalidArgumentException $e) {
                    $message = $e->getMessage();
                    $response->setCode(Response::S400_BadRequest, $message);
                } catch (\Throwable $exception) {
                    Debugger::log($exception->getMessage(), Debugger::ERROR);
                    $message = 'Application error';
                    $response->setCode(Response::S500_InternalServerError, $message);
                }
                break;
            default:
                $response->setCode(Response::S400_BadRequest, 'No route found, please check request method');
                break;
        }
        $this->terminate();
    }
}