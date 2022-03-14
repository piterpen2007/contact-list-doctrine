<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use EfTech\ContactList\Service\SearchKinsfolkService;

use EfTech\ContactList\Service\SearchKinsfolkService\KinsfolkDto;
use Psr\Log\LoggerInterface;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetKinsfolkCollectionController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
    /**
     *
     *
     * @var SearchKinsfolkService
     */
    private SearchKinsfolkService $searchKinsfolkService;
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param SearchKinsfolkService $searchKinsfolkService
     * @param ServerResponseFactory $serverResponseFactory
     */
    public function __construct(
        LoggerInterface $logger,
        SearchKinsfolkService $searchKinsfolkService,
        ServerResponseFactory $serverResponseFactory
    ) {
        $this->logger = $logger;
        $this->searchKinsfolkService = $searchKinsfolkService;
        $this->serverResponseFactory = $serverResponseFactory;
    }

    /**  Валдирует параматры запроса
     * @param ServerRequestInterface $request
     * @return string|null
     */
    private function validateQueryParams(ServerRequestInterface $request): ?string
    {
        $paramValidations = [
            'id_recipient' => 'incorrect id_recipient',
            'full_name' => 'incorrect full_name',
            'birthday' => 'incorrect birthday',
            'profession' => 'incorrect profession',
            'status' => 'incorrect status',
            'ringtone' => 'incorrect ringtone',
            'hotkey' => 'incorrect hotkey'
        ];
        $params = array_merge($request->getQueryParams(), $request->getAttributes());
        return Assert::arrayElementsIsString($paramValidations, $params);
    }


    /**
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info("Ветка kinsfolk");

        $resultOfParamValidation = $this->validateQueryParams($request);

        if (null === $resultOfParamValidation) {
            $params = array_merge($request->getQueryParams(), $request->getAttributes());
            $foundKinsfolk = $this->searchKinsfolkService->search(
                (new SearchKinsfolkService\SearchKinsfolkCriteria())
                    ->setIdRecipient($params['id_recipient'] ?? null)
                    ->setFullName($params['full_name'] ?? null)
                    ->setBirthday($params['birthday'] ?? null)
                    ->setProfession($params['profession'] ?? null)
                    ->setStatus($params['status'] ?? null)
                    ->setHotkey($params['hotkey'] ?? null)
                    ->setRingtone($params['ringtone'] ?? null)
            );

            $httpCode = $this->buildHttpCode($foundKinsfolk);
            $result = $this->buildResult($foundKinsfolk);
        } else {
            $httpCode = 500;

            $result = [
                'status' => 'fail',
                'message' => $resultOfParamValidation
            ];
        }
        return $this->serverResponseFactory->createJsonResponse($httpCode, $result);
    }
    /** Определяет http code
     * @param array $foundKinsfolk
     * @return int
     */
    protected function buildHttpCode(array $foundKinsfolk): int
    {
        return 200;
    }

    /** Подготавливает данные для ответа
     * @param array $foundKinsfolk
     * @return array
     */
    protected function buildResult(array $foundKinsfolk): array
    {
        $result = [];
        foreach ($foundKinsfolk as $foundKinsfolkItem) {
            $result[] = $this->serializeRecipient($foundKinsfolkItem);
        }
        return $result;
    }

    /**
     * @param KinsfolkDto $kinsfolkDto
     * @return array
     */
    final protected function serializeRecipient(KinsfolkDto $kinsfolkDto): array
    {
        return [
            'id_recipient' => $kinsfolkDto->getIdRecipient(),
            'full_name' => $kinsfolkDto->getFullName(),
            'birthday' => $kinsfolkDto->getBirthday(),
            'profession' => $kinsfolkDto->getProfession(),
            'status' => $kinsfolkDto->getStatus(),
            'ringtone' => $kinsfolkDto->getRingtone(),
            'hotkey' => $kinsfolkDto->getHotkey(),
            'balance' => [
                'amount' => $kinsfolkDto->getBalance()->getMoney()->getAmount(),
                'currency' => $kinsfolkDto->getBalance()->getMoney()->getCurrency()->getName()
            ]
        ];
    }
}
