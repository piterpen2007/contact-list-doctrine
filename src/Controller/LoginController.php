<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Auth\HttpAuthProvider;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\ViewTemplate\ViewTemplateInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Throwable;

class LoginController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
    private HttpAuthProvider $authProvider;
    /** Фабрика для создания ури
     * @var UriFactoryInterface
     */
    private UriFactoryInterface $uriFactory;
    /** шаблонизатор
     * @var ViewTemplateInterface
     */
    private ViewTemplateInterface $template;

    /**
     * @param ViewTemplateInterface $template
     * @param HttpAuthProvider $authProvider
     * @param ServerResponseFactory $serverResponseFactory
     * @param UriFactoryInterface $uriFactory
     */
    public function __construct(
        ViewTemplateInterface $template,
        HttpAuthProvider $authProvider,
        \EfTech\ContactList\Infrastructure\http\ServerResponseFactory $serverResponseFactory,
        \Psr\Http\Message\UriFactoryInterface $uriFactory
    ) {
        $this->template = $template;
        $this->authProvider = $authProvider;
        $this->serverResponseFactory = $serverResponseFactory;
        $this->uriFactory = $uriFactory;
    }


    /** Обработка http запроса
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->doLogin($request);
        } catch (Throwable $e) {
            $response = $this->buildErrorResponse($e);
        }
        return $response;
    }

    /**
     * @param Throwable $e
     * @return ResponseInterface
     */
    private function buildErrorResponse(Throwable $e): ResponseInterface
    {
        $httpCode = 500;
        $contex = [
            'errors' => [
                $e->getMessage()
            ]
        ];
        $html = $this->template->render(
            'errors.twig',
            $contex
        );
        return $this->serverResponseFactory->createHtmlResponse($httpCode, $html);
    }

    private function doLogin(ServerRequestInterface $request): ResponseInterface
    {
        $response = null;
        $contex = [];
        if ('POST' === $request->getMethod()) {
            $authData = [];
            parse_str($request->getBody(), $authData);

            $this->validateAuthData($authData);
            if ($this->isAuth($authData['login'], $authData['password'])) {
                $queryParams = $request->getQueryParams();
                $redirect = array_key_exists('redirect', $queryParams)
                    ? $this->uriFactory->createUri(($queryParams['redirect']))
                    : $this->uriFactory->createUri($queryParams['/']);
                $response = $this->serverResponseFactory->redirect($redirect);
            } else {
                $contex['errMsg'] = 'Логин и пароль не подходят';
            }

//            if (array_key_exists('redirect', $queryParams)) {
//                $response = ServerResponseFactory::redirect(Uri::createFromString($queryParams['redirect']));
//            }
        }
        if (null === $response) {
            $html = $this->template->render('login.twig', $contex);
            $response = $this->serverResponseFactory->createHtmlResponse(200, $html);
        }
        return $response;
    }

    /** Логика валидации данных формы аутификации
     * @param array $authData
     */
    private function validateAuthData(array $authData): void
    {
        if (false === array_key_exists('login', $authData)) {
            throw new RuntimeException('Отсутствует логин');
        }
        if (false === is_string($authData['login'])) {
            throw new RuntimeException('Логин имеет неверный формат');
        }

        if (false === array_key_exists('password', $authData)) {
            throw new RuntimeException('Отсутствует password');
        }
        if (false === is_string($authData['password'])) {
            throw new RuntimeException('password имеет неверный формат');
        }
    }

    /** Проводит аутентификацию пользователя
     * @param string $login
     * @param string $password
     * @return bool
     */
    private function isAuth(string $login, string $password): bool
    {
        return $this->authProvider->auth($login, $password);
    }
}
