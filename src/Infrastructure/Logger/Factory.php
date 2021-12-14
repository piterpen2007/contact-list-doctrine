<?php
namespace EfTech\ContactList\Infrastructure\Logger;
use Exception;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\Logger\FileLogger as FileLogger;
use EfTech\ContactList\Infrastructure\Logger\NullLogger as NullLogger;
use EfTech\ContactLIst\Infrastructure\Logger\EchoLogger as EchoLogger;
require_once __DIR__ . '/LoggerInterface.php';
require_once __DIR__ . '/../AppConfig.php';
/**
 *  Фабрика по созданию логеров
 *
 */
class Factory
{
    public function __construct()
    {
    }

    /**
     * @param AppConfig $appConfig
     * @return LoggerInterface
     * @throws Exception
     */
    public static function create(AppConfig $appConfig): LoggerInterface
    {
        if ('fileLogger' === $appConfig->getLoggerType()) {
            require_once __DIR__ . '/FileLogger/Logger.php';
            $logger = new FileLogger\Logger($appConfig->getPathToLogFile());
        } elseif ('nullLogger' === $appConfig->getLoggerType()) {
            require_once __DIR__ . '/NullLogger/Logger.php';
            $logger = new NullLogger\Logger();
        } elseif ('echoLogger' === $appConfig->getLoggerType()) {
            require_once __DIR__ . '/EchoLogger/Logger.php';
            $logger = new EchoLogger\Logger();
        } else {
            throw new Exception('Unknown logger type');
        }
        return $logger;
    }

}