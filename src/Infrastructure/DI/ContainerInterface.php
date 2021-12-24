<?php

namespace EfTech\ContactList\Infrastructure\DI;

/**
 *  Интерфейс контейнеров используемых ждя внедрения зависимоостей
 */
interface ContainerInterface
{
    /**
     * @param string $serviceName
     * @return mixed
     */
    public function get(string $serviceName);
}