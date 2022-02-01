<?php

namespace EfTech\ConctactList\Infrastructure\http;

use EfTech\ContactList\Infrastructure\http\ServerRequestFactory;
use EfTech\ContactListTest\TestUtils;

require_once __DIR__ . '/../../../vendor/autoload.php';


/**
 *  Тестирует логику работу фабрики, создающий серверный http запрос
 */
final class ServerRequestFactoryTest
{
    public static function testCreateFromGlobals(): void
    {

        echo "-----------Тестирует логику работу фабрики, создающий серверный http запрос----------\n";

        //Arrange
        $servers = [
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/samhtml/ssylki/absolyutnye-i-otnositelnye-ssylki?query=value1#fragment-example',
            'REQUEST_METHOD' => 'GET',
            'SERVER_NAME' => 'localhost',

            'HTTP_HOST'       =>  'localhost:80',
            'HTTP_CONNECTION' =>  'Keep-Alive',
            'HTTP_USER_AGENT' =>  'Apache-HttpClient\/4.5.13 (Java\/11.0.11)',
            'HTTP_COOKIE'     =>  'XDEBUG_SESSION=16151',
        ];
        $expectedBody = 'test';
        //Act
        $httpServerRequest = ServerRequestFactory::createFromGlobals($servers, $expectedBody);
        //Assert
        $expected = 'http://localhost:80/samhtml/ssylki' .
            '/absolyutnye-i-otnositelnye-ssylki?query=value1#fragment-example';
        $actual = (string)$httpServerRequest->getUri();

        //Assert
        if ($expected === $actual) {
            echo "      ОК - объект ServerRequestFactory корректно создан\n";
        } else {
            echo  "      FAIL - объект ServerRequestFactory не корректно создан, ожидалось $expected.\n
             Актуальное значение $actual\n";
        }

        if ($expectedBody === $httpServerRequest->getBody()) {
            echo "    OK - корректное тело запроса\n";
        } else {
            echo  "    FAIL - не корректное тело запроса, ожидалось $expectedBody.\n Актуальное значение 
            {$httpServerRequest->getBody()}\n";
        }

        $expectedProtocolVersion = '1.1';
        if ($expectedProtocolVersion === $httpServerRequest->getProtocolVersion()) {
            echo "    OK - корректая версия http протокола\n";
        } else {
            echo  "    FAIL - не корректая версия http протокола, ожидалось $expectedProtocolVersion.\n 
            Актуальное значение {$httpServerRequest->getProtocolVersion()}\n";
        }

        $expectedMethod = 'GET';
        if ($expectedMethod === $httpServerRequest->getMethod()) {
            echo "    OK - корректый тип метода\n";
        } else {
            echo  "    FAIL - не корректый тип метода, ожидалось $expectedMethod.\n Актуальное значение 
            {$httpServerRequest->getMethod()}\n";
        }

        $expectedRequestTarget = '/samhtml/ssylki/absolyutnye-i-otnositelnye-ssylki?query=value1#fragment-example';
        if ($expectedRequestTarget === $httpServerRequest->getRequestTarget()) {
            echo "    OK - корректая цель http запроса\n";
        } else {
            echo  "    FAIL - не корректая цель http запроса, ожидалось $expectedRequestTarget.\n Актуальное значение 
            {$httpServerRequest->getRequestTarget()}\n";
        }

        $actualQueryParams = $httpServerRequest->getQueryParams();

        $expectedQueryParams = [
            'query' => 'value1'
        ];

        $unnecessaryQueryParams = TestUtils::arrayDiffAssocRecursive($actualQueryParams, $expectedQueryParams);

        $missingQueryParams =  TestUtils::arrayDiffAssocRecursive($expectedQueryParams, $actualQueryParams);

        $errMsg = '';

        if (count($unnecessaryQueryParams) > 0) {
            $errMsg .= sprintf(
                "         Есть лишние элементы %s\n",
                json_encode($unnecessaryQueryParams, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
            );
        }
        if (count($missingQueryParams) > 0) {
            $errMsg .= sprintf(
                "         Есть лишние недостающие элементы %s\n",
                json_encode($missingQueryParams, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
            );
        }

        if ('' === $errMsg) {
            echo "    ОК- данные параметров запросов валидны\n";
        } else {
            echo "    FAIL - данные параметров запросов не валидны\n" . $errMsg;
        }
    }
}

ServerRequestFactoryTest::testCreateFromGlobals();
