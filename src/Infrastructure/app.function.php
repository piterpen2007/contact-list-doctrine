<?php
namespace EfTech\ContactList\Infrastructure;
use EfTech\ContactList\Infrastructure\http\httpResponse;

/**
 * @param string $sourceName - путь до файла
 * @return array - вывод содержимого файла в виде массива
 */
function loadData (string $sourceName):array
{
    $content = file_get_contents($sourceName);
    return json_decode($content, true,512 , JSON_THROW_ON_ERROR);
}

/** Отображает результат клиенту
 * @param httpResponse $response
 */
function render(httpResponse $response):void
{
    foreach ($response->getHeaders() as $headerName => $headerValue) {
        header("$headerName: $headerValue");
    }
    http_response_code($response->getStatusCode());
    echo $response->getBody();
    exit();
}

/**
 * Функция валидации
 *
 * @param array $validateParameters - валидируемые параметры, ключ имя параметра, а значение это текст сообщения о ошибке
 * @param array $params - все множество параметров
 * @return array - сообщение о ошибках
 */
function paramTypeValidation(array $validateParameters, array $params):?array
{
    $result = null;
    foreach ($validateParameters as $paramName => $errMsg) {
        if (array_key_exists($paramName, $params) && false === is_string($params[$paramName])) {
            $result = [
                'httpCode' => '500',
                'result' => [
                    'status' => 'fail',
                    'message' => $errMsg
                ]
            ];
            break;
        }
    }
    return $result;
}