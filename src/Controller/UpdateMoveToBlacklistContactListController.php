<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Service\MoveToBlacklistContactListService;
use EfTech\ContactList\Service\MoveToBlacklistService\Exception\ContactListNotFoundException;
use EfTech\ContactList\Service\MoveToBlacklistService\Exception\RuntimeException;
use EfTech\ContactList\Service\MoveToBlacklistService\MoveToBlacklistDto;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class UpdateMoveToBlacklistContactListController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
    /**
     * @var MoveToBlacklistContactListService
     */
    private MoveToBlacklistContactListService $moveToBlacklistContactListService;

    /**
     * @param MoveToBlacklistContactListService $moveToBlacklistContactListService
     * @param ServerResponseFactory $serverResponseFactory
     */
    public function __construct(
        MoveToBlacklistContactListService $moveToBlacklistContactListService,
        \EfTech\ContactList\Infrastructure\http\ServerResponseFactory $serverResponseFactory
    ) {
        $this->moveToBlacklistContactListService = $moveToBlacklistContactListService;
        $this->serverResponseFactory = $serverResponseFactory;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $attributes = $request->getAttributes();
            if (false === array_key_exists('id_recipient', $attributes)) {
                throw new RuntimeException('there is no information about the id of the text document');
            }
            $resultDto = $this->moveToBlacklistContactListService->move((int)$attributes['id_recipient']);
            $httpCode = 200;
            $jsonData = $this->buildJsonData($resultDto);
        } catch (ContactListNotFoundException $e) {
            $httpCode = 404;
            $jsonData = ['status' => 'fail', 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            $httpCode = 500;
            $jsonData = ['status' => 'fail', 'message' => $e->getMessage()];
        }

        return $this->serverResponseFactory->createJsonResponse($httpCode, $jsonData);
    }

    /** Подготавливает данные для успешного ответа на основе dto сервиса
     * @param MoveToBlacklistDto $resultDto
     * @return array
     */
    private function buildJsonData(MoveToBlacklistDto $resultDto): array
    {
        return [
            'blacklist' => $resultDto->isBlackList()
        ];
    }
}
