<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Auth\HttpAuthProvider;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Uri\Uri;
use EfTech\ContactList\Infrastructure\ViewTemplate\ViewTemplateInterface;

class LoginController implements ControllerInterface
{
    private HttpAuthProvider $authProvider;
    /** шаблонизатор
     * @var ViewTemplateInterface
     */
    private ViewTemplateInterface $template;

    /**
     * @param ViewTemplateInterface $template
     * @param HttpAuthProvider $authProvider
     */
    public function __construct(ViewTemplateInterface $template,
        HttpAuthProvider $authProvider
    )
    {
        $this->template = $template;
        $this->authProvider = $authProvider;
    }


    /** Обработка http запроса
     * @param ServerRequest $request
     * @return httpResponse
     */
    public function __invoke(ServerRequest $request): httpResponse
    {
        try {
            $response = $this->doLogin($request);
        } catch (\Throwable $e) {
            $response = $this->buildErrorResponse($e);
        }
        return $response;

    }

    /**
     * @param \Throwable $e
     * @return httpResponse
     */
    private function buildErrorResponse(\Throwable $e):httpResponse
    {
        $httpCode = 500;
        $contex = [
            'errors' => [
                $e->getMessage()
            ]
        ];
        $html = $this->template->render(
            __DIR__ . '/../../templates/errors.phtml',
            $contex
        );
        return ServerResponseFactory::createHtmlResponse($httpCode,$html);
    }

    private function doLogin(ServerRequest $request):httpResponse
    {
        $response = null;
        $contex = [];
        if ('POST' === $request->getMethod()) {

            $authData = [];
            parse_str($request->getBody(),$authData);

            $this->validateAuthData($authData);
            if ($this->isAuth($authData['login'],$authData['password'])) {
                $queryParams = $request->getQueryParams();
                $redirect = array_key_exists('redirect',$queryParams)
                    ? Uri::createFromString($queryParams['redirect'])
                    : Uri::createFromString($queryParams['/']);
                $response = ServerResponseFactory::redirect($redirect);
            } else {
                $contex['errMsg'] = 'Логин и пароль не подходят';
            }

//            if (array_key_exists('redirect', $queryParams)) {
//                $response = ServerResponseFactory::redirect(Uri::createFromString($queryParams['redirect']));
//            }
        }
        if (null === $response) {
            $html = $this->template->render(__DIR__ . '/../../templates/login.phtml',$contex);
            $response = ServerResponseFactory::createHtmlResponse(200,$html);
        }
        return $response;
    }

    /** Логика валидации данных формы аутификации
     * @param array $authData
     */
    private function validateAuthData(array $authData):void
    {
        if (false === array_key_exists('login',$authData)) {
            throw new RuntimeException('Отсутствует логин');
        }
        if (false === is_string($authData['login'])) {
            throw new RuntimeException('Логин имеет неверный формат');
        }

        if (false === array_key_exists('password',$authData)) {
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
    private function isAuth(string $login,string $password):bool
    {
        return $this->authProvider->auth($login,$password);
    }
}