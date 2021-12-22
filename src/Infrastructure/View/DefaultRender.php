<?php

namespace EfTech\ContactList\Infrastructure\View;

use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\View\RenderInterface;


/** Логика отображения ответа пользователя по умолчанию
*
*/
final class DefaultRender implements RenderInterface
{
    /**
     * @param httpResponse $httpResponse
     * @return void
     */
    public function render(httpResponse $httpResponse): void
    {
        foreach ($httpResponse->getHeaders() as $headerName => $headerValue) {
            header("$headerName: $headerValue");
        }
        http_response_code($httpResponse->getStatusCode());
        echo $httpResponse->getBody();
    }
}
