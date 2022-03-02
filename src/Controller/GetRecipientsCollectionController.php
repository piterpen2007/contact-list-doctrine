<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use Psr\Log\LoggerInterface;
use EfTech\ContactList\Service\SearchRecipientsService\RecipientDto;
use EfTech\ContactList\Service\SearchRecipientsService\SearchRecipientsCriteria;
use EfTech\ContactList\Service\SearchRecipientsService;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetRecipientsCollectionController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
    /**
     *
     *
     * @var SearchRecipientsService
     */
    private SearchRecipientsService $searchRecipientsService;
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param SearchRecipientsService $searchRecipientsService
     * @param ServerResponseFactory $serverResponseFactory
     */
    public function __construct(
        LoggerInterface $logger,
        SearchRecipientsService $searchRecipientsService,
        ServerResponseFactory $serverResponseFactory
    ) {
        $this->logger = $logger;
        $this->searchRecipientsService = $searchRecipientsService;
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
            'profession' => 'incorrect profession'
        ];
        $params = array_merge($request->getQueryParams(), $request->getAttributes());
        return Assert::arrayElementsIsString($paramValidations, $params);
    }


    /**
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info("Ветка recipient");

        $resultOfParamValidation = $this->validateQueryParams($request);

        if (null === $resultOfParamValidation) {
            $params = array_merge($request->getQueryParams(), $request->getAttributes());
            $foundRecipients = $this->searchRecipientsService->search(
                (new SearchRecipientsCriteria())
                    ->setIdRecipient($params['id_recipient'] ?? null)
                    ->setFullName($params['full_name'] ?? null)
                    ->setBirthday($params['birthday'] ?? null)
                    ->setProfession($params['profession'] ?? null)
            );

            $httpCode = $this->buildHttpCode($foundRecipients);
            $result = $this->buildResult($foundRecipients);
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
     * @param array $foundRecipients
     * @return int
     */
    protected function buildHttpCode(array $foundRecipients): int
    {
        return 200;
    }

    /** Подготавливает данные для ответа
     * @param array $foundRecipients
     * @return array
     */
    protected function buildResult(array $foundRecipients): array
    {
        $result = [];
        foreach ($foundRecipients as $foundRecipient) {
            $result[] = $this->serializeRecipient($foundRecipient);
        }
        return $result;
    }

    /**
     * @param RecipientDto $recipientDto
     * @return array
     */
    final protected function serializeRecipient(RecipientDto $recipientDto): array
    {
        return [
            'id_recipient' => $recipientDto->getIdRecipient(),
            'full_name' => $recipientDto->getFullName(),
            'birthday' => $recipientDto->getBirthday(),
            'profession' => $recipientDto->getProfession(),
            'balance' => [
                'amount' => $recipientDto->getBalance()->getMoney()->getAmount(),
                'currency' => $recipientDto->getBalance()->getMoney()->getCurrency()->getName()
            ]
        ];
    }
}
