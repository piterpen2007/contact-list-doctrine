<?php

namespace EfTech\ContactList\Exception;

use EfTech\ContactList\Infrastructure\Exception as BaseException;

/**
 * Исключение бросаетс в случае если не удалось создать конфиг приложения
 */
class ErrorCreateAppConfigException extends BaseException\ErrorCreateAppConfigException implements ExceptionInterface
{
}
