<?php

namespace EfTech\ContactList\Infrastructure\Router;

use EfTech\ContactList\Infrastructure\http\ServerRequest;

interface RouterInterface
{
    /** Возвращает обработчик запроса
     * @param ServerRequest $serverRequest - объект серверного http запроса
     * @return callable|null
     */
    public function getDispatcher(ServerRequest $serverRequest): ?callable;
}