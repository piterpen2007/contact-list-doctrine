<?php
namespace EfTech\ContactList\Infrastructure\http\Exception;

use \EfTech\ContactList\Exception\RuntimeException;

/**
 *  Исключение выбрасывается в случае если не удалось создать объект http запроса
 */
class ErrorHttpRequestException extends RuntimeException
{

}