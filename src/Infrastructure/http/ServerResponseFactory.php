<?php

namespace EfTech\ContactList\Infrastructure\http;

use EfTech\ContactList\Exception\RuntimeException;
use Throwable;

class ServerResponseFactory
{
    /**
     *  Расшифровка http кодов
     */
    private const PHRASES = [
        200 => 'OK',
        404 => 'Not found',
        503 => 'Service Unavailable',
        500 => 'Internal Server Error'
    ];

    /** Создаёт http ответ с данными
     * @param int $code
     * @param array $data
     * @return httpResponse
     */
    public static function createJsonResponse(int $code,$data):httpResponse
    {
        try {

            $body = json_encode($data, JSON_THROW_ON_ERROR);
            if ( false === array_key_exists($code, self::PHRASES)) {
                throw new RuntimeException('Некорректный код ответа');
            }

            $phrases = '';
        } catch (Throwable $e) {
            $body = '{"status": "fail", "message": "response coding error"}';
            $code = 520;
            $phrases = 'Unknown error';
        }
        return new httpResponse('1.1', ['Content-Type' => 'application/json'],$body, $code  ,$phrases);
    }

}