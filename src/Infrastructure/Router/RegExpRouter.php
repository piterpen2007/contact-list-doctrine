<?php

namespace EfTech\ContactList\Infrastructure\Router;

use EfTech\ContactList\Infrastructure\http\ServerRequest;

/**
 * Роутер сопоставляющий регулярные выражения и обработчик
 */
final class RegExpRouter implements RouterInterface
{

    /**
     * @inheritDoc
     */
    public function getDispatcher(ServerRequest $serverRequest): ?callable
    {
        return null;
    }
}