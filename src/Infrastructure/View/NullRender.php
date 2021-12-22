<?php

namespace EfTech\ContactList\Infrastructure\View;


use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\View\RenderInterface;

/** Рендер заглушка
 *
 */
final class NullRender implements RenderInterface
{

    public function render(httpResponse $httpResponse): void
    {
        // TODO: Implement render() method.
    }
}