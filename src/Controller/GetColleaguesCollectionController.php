<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use EfTech\ContactList\Service\SearchColleagueService;
use EfTech\ContactList\Service\SearchColleagueService\ColleagueDto;
use EfTech\ContactList\Service\SearchColleagueService\SearchColleagueCriteria;
use Psr\Log\LoggerInterface;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetColleaguesCollectionController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
    /**
     *
     *
     * @var SearchColleagueService
     */
    private SearchColleagueService $searchColleagueService;
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param SearchColleagueService $searchColleagueService
     * @param ServerResponseFactory $serverResponseFactory
     */
    public function __construct(
        LoggerInterface $logger,
        SearchColleagueService $searchColleagueService,
        ServerResponseFactory $serverResponseFactory
    ) {
        $this->logger = $logger;
        $this->searchColleagueService = $searchColleagueService;
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
            'department' => 'incorrect department',
            'position' => 'incorrect position',
            'room_number' => 'incorrect room_number'
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
            $foundColleagues = $this->searchColleagueService->search(
                (new SearchColleagueCriteria())
                    ->setIdRecipient($params['id_recipient'] ?? null)
                    ->setFullName($params['full_name'] ?? null)
                    ->setBirthday($params['birthday'] ?? null)
                    ->setProfession($params['profession'] ?? null)
                    ->setDepartment($params['department'] ?? null)
                    ->setPosition($params['position'] ?? null)
                    ->setRoomNumber($params['room_number'] ?? null)
            );

            $httpCode = $this->buildHttpCode($foundColleagues);
            $result = $this->buildResult($foundColleagues);
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
     * @param array $foundColleagues
     * @return int
     */
    protected function buildHttpCode(array $foundColleagues): int
    {
        return 200;
    }

    /** Подготавливает данные для ответа
     * @param array $foundColleagues
     * @return array
     */
    protected function buildResult(array $foundColleagues): array
    {
        $result = [];
        foreach ($foundColleagues as $foundColleague) {
            $result[] = $this->serializeRecipient($foundColleague);
        }
        return $result;
    }

    /**
     * @param ColleagueDto $colleagueDto
     * @return array
     */
    final protected function serializeRecipient(ColleagueDto $colleagueDto): array
    {
        return [
            'id_recipient' => $colleagueDto->getIdRecipient(),
            'full_name' => $colleagueDto->getFullName(),
            'birthday' => $colleagueDto->getBirthday(),
            'profession' => $colleagueDto->getProfession(),
            'department' => $colleagueDto->getDepartment(),
            'position' => $colleagueDto->getPosition(),
            'room_number' => $colleagueDto->getRoomNumber(),
            'balance' => [
                'amount' => $colleagueDto->getBalance()->getMoney()->getAmount(),
                'currency' => $colleagueDto->getBalance()->getMoney()->getCurrency()->getName()
            ]
        ];
    }
}
