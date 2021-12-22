<?php
namespace EfTech\ContactList\Infrastructure\Controller;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;

/**
 * Интерфейс контроллера
 */
interface ControllerInterface
{
    /** Обработка http запроса
     * @param ServerRequest $request
     * @return httpResponse
     */
    public function __invoke(ServerRequest $request): httpResponse;
}