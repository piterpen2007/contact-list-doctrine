<?php

namespace EfTech\ContactList\Infrastructure;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\DI\ContainerInterface;
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
     * @var array|null Обработчики запросов
     */
    private ?array $handlers = null;
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
    /** Локатор сервисов
     * @var ContainerInterface |null
     */
    private ?ContainerInterface $container = null;

    /** Фабрика для создания компонента отвечающего за рендеринг результатов
     * @var callable
     */
    private $renderFactory;
    /** Фабрика реализующая логику создания di контейнера
     * @var callable
     */
    private $diContainerFactory;
    /** Фабрика реализующая логику создания обработчика запроса
     * @var callable
     */
    private $handlersFactory;

    /**
     * @param callable $handlersFactory
     * @param callable $loggerFactory
     * @param callable $appConfigFactory
     * @param callable $renderFactory
     * @param callable $diContainerFactory
     */
    public function __construct(
        callable $handlersFactory,
        callable $loggerFactory,
        callable $appConfigFactory,
        callable $renderFactory,
        callable $diContainerFactory
    ) {
        $this->loggerFactory = $loggerFactory;
        $this->appConfigFactory = $appConfigFactory;
        $this->renderFactory = $renderFactory;
        $this->diContainerFactory = $diContainerFactory;
        $this->handlersFactory = $handlersFactory;
        $this->initErrorHandling();
    }

    /** Инициация обработки ошибок
     *
     */
    private function initErrorHandling(): void
    {
        set_error_handler(static function (int $errNom, string $errStr) {
            throw new Exception\RuntimeException($errStr);
        });
    }



    /** Возвращает обработчики запросов
     * @return array|null
     */
    public function getHandlers(): ?array
    {
        if (null === $this->handlers) {
            $this->handlers = ($this->handlersFactory)($this->getContainer());
        }
        return $this->handlers;
    }

    /**
     * @return AppConfig|null
     */
    public function getAppConfig(): AppConfig
    {
        if (null === $this->appConfig) {
            $this->appConfig = ($this->appConfigFactory)($this->getContainer());
        }
        return $this->appConfig;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if (null === $this->logger) {
            $this->logger = ($this->loggerFactory)($this->getContainer());
        }
        return $this->logger;
    }

    /**
     * @return RenderInterface
     */
    public function getRender(): RenderInterface
    {
        if (null === $this->render) {
            $this->render = ($this->renderFactory)($this->getContainer());
        }
        return $this->render;
    }

    /**
     * @return ContainerInterface|null
     */
    public function getContainer(): ?ContainerInterface
    {
        if (null === $this->container) {
            $this->container = ($this->diContainerFactory)();
        }
        return $this->container;
    }

    /** Возвращает контроллер
     * @param string $urlPath
     * @return callable
     */
    private function getController(string $urlPath):callable
    {
        $handlers = $this->handlers;
        if(is_callable($handlers[$urlPath])) {
            $controller = $handlers[$urlPath];
        } elseif (is_string($handlers[$urlPath]) &&
            is_subclass_of($handlers[$urlPath], ControllerInterface::class, true)) {
            $controller = $this->getContainer()->get($handlers[$urlPath]);
        } else {
            throw new RuntimeException("Для url '$urlPath' зарегистрирован некорректный обработчик");
        }
        return $controller;
    }

























    /** Обработчик запроса
     * @param ServerRequest $serverRequest - объект серверного http запроса
     * @return httpResponse - реез ответ
     */
    public function dispath(ServerRequest $serverRequest): httpResponse
    {
        $hasAppConfig = false;
        try {
            $hasAppConfig = $this->getAppConfig() instanceof AppConfig;
            $logger = $this->getLogger();

            $urlPath = $serverRequest->getUri()->getPath();
            $logger->log('Url request received' . $urlPath);

            if (array_key_exists($urlPath,$this->getHandlers())) {

                $controller = $this->getController($urlPath);
                $httpResponse = $controller($serverRequest);

                if (!($httpResponse instanceof httpResponse)) {
                    throw new Exception\UnexpectedValueException('Контроллер вернул некорректный результат');
                }
            } else {
                $httpResponse = ServerResponseFactory::createJsonResponse(
                    404,
                    ['status' => 'fail', 'message' => 'unsupported request']
                );
            }
            $this->getRender()->render($httpResponse);
        } catch (Exception\invalidDataStructureException $e) {
            $httpResponse = ServerResponseFactory::createJsonResponse(
                503,
                ['status' => 'fail', 'message' => $e->getMessage()]
            );
            $this->silentRender($httpResponse);
        } catch (Throwable $e) {
            $errMsg = ($hasAppConfig && !$this->getAppConfig()->isHideErrorMsg())
            || $e instanceof Exception\ErrorCreateAppConfigException
                ? $e->getMessage()
                : 'system error';

            $this->silentLog($e->getMessage());

            $httpResponse = ServerResponseFactory::createJsonResponse(
                500,
                ['status' => 'fail', 'message' => $errMsg]
            );
            $this->silentRender($httpResponse);
        }
        return $httpResponse;
    }
    /** Тихое отображение данных - если отправка данных пользователю закончилось ошибкой, то это никак не влияет
     * @param httpResponse $httpResponse - http ответ
     */
    private function silentRender(httpResponse $httpResponse): void
    {
        try {
            $this->getRender()->render($httpResponse);
        } catch (Throwable $e) {
            $this->silentLog($e->getMessage());
        }
    }

    /** Тихое логгирование - если отправка данных пользователю закончилось ошибкой, то это никак не влияет
     * @param string $msg - сообщение в логи
     */
    private function silentLog(string $msg):void
    {
        try {
            $this->logger->log($msg);
        } catch (Throwable $e) {

        }
    }

}


