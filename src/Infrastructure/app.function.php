<?php
namespace EfTech\ContactList\Infrastructure;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use Throwable;
use UnexpectedValueException;
require_once __DIR__ . '/AppConfig.php';
require_once __DIR__ . '/Logger/LoggerInterface.php';
require_once __DIR__ . '/invalidDataStructureException.php';
/** Функция перводит данные из json формата в php и возвращает содержимое
 *
 * @param string $sourceName - имя файла
 * @return array - содержимое json файла
 */
function loadData (string $sourceName):array
{
    $content = file_get_contents($sourceName);
    return json_decode($content, true);
}

/**
 * Функция вывода данных
 *
 * @param array $data - данные, которые хотим отобразить
 * @param int $httpCode - http code
 */
function render(array $data, int $httpCode)
{
    header('Content-Type: application/json');
    http_response_code($httpCode);
    echo json_encode($data);
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
    foreach ($validateParameters as $paramName => $errorMessage) {
        if (array_key_exists($paramName, $params) && false === is_string($params[$paramName])) {
            $result = [
                'httpCode' => 500,
                'result' => [
                    'status' => 'fail',
                    'message'=> $errorMessage
                ]
            ];
            break;
        }
    }
    return $result;
}
/** Функция реализации веб приложения
 * @param $handler array - массив сопоставляющий url parh с функциями реализующими логику обработки запроса
 * @param $requestUri string - URI запроса
 * @param $loggerFactory callable - фабрика логгеров
 * @param callable $appConfigFactory
 * @return array
 * @throws UnexpectedValueException
 */
function app (array $handler, string $requestUri, callable $loggerFactory, callable $appConfigFactory):array
{
    try {
        $query = parse_url($requestUri, PHP_URL_QUERY);
        $requestParams = [];
        parse_str($query,$requestParams);

        $appConfig = $appConfigFactory();

        if (!$appConfig instanceof AppConfig) {
            throw new UnexpectedValueException('incorrect application config');
        }
        $logger = $loggerFactory($appConfig);
        if (!($logger instanceof LoggerInterface)) {
            throw new UnexpectedValueException('incorrect logger');
        }

        $logger->log('Url request received' . $requestUri);
        $urlPath = parse_url($requestUri, PHP_URL_PATH);

        if(array_key_exists($urlPath, $handler)) {
            $result = $handler[$urlPath]($requestParams,$logger,$appConfig);
        } else {
            $result = [
                'httpCode' => 404,
                'result' => [
                    'status' => 'fail',
                    'message' => 'unsupported request'
                ]
            ];
            $logger->log($result['result']['message']);
        }
    } catch (invalidDataStructureException $e) {
        $result = [
            'httpCode' => 503,
            'result' => [
                'status' => 'fail',
                'message' => $e->getMessage()
            ]
        ];
    } catch (Throwable $e) {

        $result = [
            'httpCode' => 500,
            'result' => [
                'status' => 'fail',
                'message' => $e->getMessage()
            ]
        ];
    }
    return $result;
}