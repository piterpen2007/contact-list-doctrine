<?php

namespace EfTech\ContactList\Infrastructure\http;

/**
 *  Серверный запрос
 */
class ServerRequest extends httpRequest
{
    /** Параметры запроса
     * @var array|null
     */
    private ?array $queryParams = null;

    /** Возвращает параметры запроса
     * @return array
     */
    public function getQueryParams():array
    {
        if(null === $this->queryParams) {
            $queryParams = [];
            parse_str($this->getUri()->getQuery(),$queryParams);
            $this->queryParams = $queryParams;
        }

        return  $this->queryParams;
    }
}