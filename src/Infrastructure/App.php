<?php

namespace EfTech\ContactList\Infrastructure;
use EfTech\ContactList\Exception\UnexpectedValueException;
use EfTech\ContactList\Infrastructure\View\RenderInterface;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use Throwable;
use EfTech\ContactList\Exception;
/**
 * Ядро приложения
 */
final class App
{
    /**
     * @var array Обработчики запросов
     */
    private array $handlers;
    /** Фабрика для создания логгеров
     * @var callable
     */
    private $loggerFactory;
    /**     Фабрика для создания конфига приложения
     * @var callable
     */
    private $appConfigFactory;
    /** Конфиг приложения
     * @var AppConfig|null
     */
    private ?AppConfig $appConfig = null;
    /** Логирование
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger = null;
    /** Компонент отвечающий за рендеринг
     * @var RenderInterface|null
     */
    private ?RenderInterface $render = null;

    /** Фабрика для создания компонента отвечающего за рендеринг результатов
     * @var callable
     */
    private $renderFactory;

    /** Инициация обработки ошибок
     *
     */
    private function initErrorHandling(): void
    {
        set_error_handler(static function (int $errNom, string $errStr) {
            throw new Exception\RuntimeException($errStr);
        });
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if (null === $this->logger) {
            $logger = call_user_func($this->loggerFactory, $this->getAppConfig());
            if (!($logger instanceof LoggerInterface)) {
                throw new UnexpectedValueException('incorrect logger');
            }
            $this->logger = $logger;
        }
        return $this->logger;
    }


    /**
     * @param array $handler - Обработчики запросов
     * @param callable $loggerFactory - Фабрика для создания логгеров
     * @param callable $appConfigFactory - Фабрика для создания конфига приложения
     */
    public function __construct(array $handler, callable $loggerFactory, callable $appConfigFactory,callable $renderFactory)
    {
        $this->handlers = $handler;
        $this->loggerFactory = $loggerFactory;
        $this->renderFactory = $renderFactory;
        $this->appConfigFactory = $appConfigFactory;
        $this->initErrorHandling();
    }

    /**
    * @return RenderInterface
    */
    private function getRender(): RenderInterface
    {
        if (null === $this->render) {
            $renderFactory = $this->renderFactory;
            $render = $renderFactory();
            if (!($render instanceof RenderInterface)) {
                throw new UnexpectedValueException('incorrect render');
            }
            $this->render = $render;
        }
        return $this->render;
    }
    /**
     * @return AppConfig
     */
    private function getAppConfig(): AppConfig
    {
        if (null === $this->appConfig) {
            try {
                $appConfig = call_user_func($this->appConfigFactory);
            } catch (Throwable $e) {
                throw new Exception\ErrorCreateAppConfigException($e->getMessage(), $e->getCode(), $e);
            }

            if (!($appConfig instanceof AppConfig)) {
                throw new Exception\ErrorCreateAppConfigException('incorrect application config');
            }
            $this->appConfig = $appConfig;
        }
        return $this->appConfig;
    }

    /** Обработчик запроса
     * @param ServerRequest $serverRequest - объект серверного http запроса
     * @return httpResponse - реез ответ
     */
    public function dispath(ServerRequest $serverRequest): httpResponse
    {
        $appConfig = null;
        try {
            $appConfig = $this->getAppConfig();
            $logger = $this->getLogger();

            $urlPath = $serverRequest->getUri()->getPath();
            $logger->log('Url request received' . $urlPath);

            if (array_key_exists($urlPath, $this->handlers)) {
                $httpResponse = call_user_func($this->handlers[$urlPath], $serverRequest, $logger, $appConfig);
                if (!($httpResponse instanceof httpResponse)) {
                    throw new Exception\UnexpectedValueException('Контроллер вернул некорректный результат');
                }
            } else {
                $httpResponse = ServerResponseFactory::createJsonResponse(
                    404,
                    ['status' => 'fail', 'message' => 'unsupported request']
                );
            }
        } catch (Exception\invalidDataStructureException $e) {
            $httpResponse = ServerResponseFactory::createJsonResponse(
                503,
                ['status' => 'fail', 'message' => $e->getMessage()]
            );
        } catch (Throwable $e) {
            $errMsg = ($appConfig instanceof AppConfig && !$appConfig->isHideErrorMsg())
            || $e instanceof Exception\ErrorCreateAppConfigException
                ? $e->getMessage()
                : 'system error';
            try {
                $this->getLogger()->log($e->getMessage());
            } catch (Throwable $e) {
            }
            $httpResponse = ServerResponseFactory::createJsonResponse(
                500,
                ['status' => 'fail', 'message' => $errMsg]
            );
        }
        $this->getRender()->render($httpResponse);
        return $httpResponse;
    }
}


