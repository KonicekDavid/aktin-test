<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Presentation\Api\Article;

use App\DTO\UserRole;
use App\Model\Facade\ArticleFacade;
use App\Presentation\Api\BaseApiPresenter;
use Nette\Application\Attributes\Requires;
use Nette\Application\BadRequestException;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Tracy\Debugger;

class ArticlePresenter extends BaseApiPresenter {

    /** @var ArticleFacade $articleFacade @inject */
    public ArticleFacade $articleFacade;

    #[Requires(methods: ['GET', 'POST', 'PUT', 'DELETE'], forward: false, actions: 'default')]
    public function actionDefault(?int $id): void {
        $response = $this->getHttpResponse();

        switch ($this->getHttpRequest()->getMethod()) {
            case 'GET':
                if ($id !== null) {
                    $article = $this->articleFacade->getById($id);
                    $article ? $this->sendJson($article) : $this->error('Article not found', IResponse::S404_NotFound);
                } else {
                    $this->sendJson($this->articleFacade->getAll());
                }
                break;
            case 'POST':
                $this->requireRole([UserRole::AUTHOR, UserRole::ADMIN]);
                $data = json_decode($this->getHttpRequest()->getRawBody() ?? '', true) ?? [];
                try {
                    $this->articleFacade->create($data, $this->user);
                    $response->setCode(Response::S201_Created);
                } catch (\InvalidArgumentException $e) {
                    $response->setCode(Response::S400_BadRequest, $e->getMessage());
                } catch (\Throwable $exception) {
                    Debugger::log($exception->getMessage(), Debugger::ERROR);
                    $message = 'Application error';
                    $response->setCode(Response::S500_InternalServerError, $message);
                }
                break;
            case 'PUT':
                $this->requireRole([UserRole::AUTHOR, UserRole::ADMIN]);
                $data = json_decode($this->getHttpRequest()->getRawBody() ?? '', true) ?? [];
                try {
                    if ($id !== null) {
                        $article = $this->articleFacade->getById($id);
                        if (!$article) {
                            $this->error('Article not found', IResponse::S404_NotFound);
                        }
                        if ($this->userRole === UserRole::AUTHOR) {
                            if ($article->author->id !== $this->user->getId()) {
                                $this->error('You do not own this article', IResponse::S403_Forbidden);
                            }
                        }
                        $this->articleFacade->update($article, $data);
                        $response->setCode(Response::S200_OK);
                    } else {
                        $response->setCode(Response::S400_BadRequest, 'Specified id is missing in url');
                    }
                } catch (BadRequestException $e) {
                    $response->setCode($e->getCode(), $e->getMessage());
                } catch (\InvalidArgumentException $e) {
                    $response->setCode(Response::S400_BadRequest, $e->getMessage());
                } catch (\Throwable $exception) {
                    Debugger::log($exception->getMessage(), Debugger::ERROR);
                    $message = 'Application error';
                    $response->setCode(Response::S500_InternalServerError, $message);
                }
                break;
            case 'DELETE':
                $this->requireRole([UserRole::AUTHOR, UserRole::ADMIN]);
                try {
                    if ($id !== null) {
                        $article = $this->articleFacade->getById($id);
                        if (!$article) {
                            $this->error('Article not found', IResponse::S404_NotFound);
                        }
                        if ($this->userRole === UserRole::AUTHOR) {
                            if ($article->author->id !== $this->user->getId()) {
                                $this->error('You do not own this article', IResponse::S403_Forbidden);
                            }
                        }
                        $this->articleFacade->remove($article);
                        $response->setCode(Response::S200_OK);
                    } else {
                        $response->setCode(Response::S400_BadRequest, 'Specified id is missing in url');
                    }
                } catch (BadRequestException $e) {
                    $response->setCode($e->getCode(), $e->getMessage());
                } catch (\InvalidArgumentException $e) {
                    $response->setCode(Response::S400_BadRequest, $e->getMessage());
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