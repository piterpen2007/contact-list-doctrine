<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use Psr\Log\LoggerInterface;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use EfTech\ContactList\Service\SearchAddressService;
use EfTech\ContactList\Service\SearchAddressService\AddressDto;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAddressCollectionController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
    /** Логгер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     *
     *
     * @var SearchAddressService
     */
    private SearchAddressService $contactListService;

    /**
     * @param LoggerInterface $logger
     * @param SearchAddressService $contactListService
     * @param ServerResponseFactory $serverResponseFactory
     */
    public function __construct(
        LoggerInterface $logger,
        SearchAddressService $contactListService,
        \EfTech\ContactList\Infrastructure\http\ServerResponseFactory $serverResponseFactory
    ) {
        $this->logger = $logger;
        $this->contactListService = $contactListService;
        $this->serverResponseFactory = $serverResponseFactory;
    }

    /**  Валдирует параматры запроса
     * @param ServerRequestInterface $request
     * @return string|null
     */
    private function validateQueryParams(ServerRequestInterface $request): ?string
    {
        $paramTypeValidation = [
            'id_address' => "incorrect id_address",
            'id_recipient' => 'incorrect id_recipient',
            'address' => 'incorrect address',
            'status' => 'incorrect status'
        ];
        $queryParams = array_merge($request->getQueryParams(), $request->getAttributes());
        return Assert::arrayElementsIsString($paramTypeValidation, $queryParams);
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info("Ветка address");
        $resultOfParamValidation = $this->validateQueryParams($request);

        if (null === $resultOfParamValidation) {
            $params = array_merge($request->getQueryParams(), $request->getAttributes());
            $foundAddresses = $this->contactListService
                ->search((new SearchAddressService\SearchAddressCriteria())
                    ->setIdAddress($params['id_address'] ?? null)
                    ->setIdRecipient(isset($params['id_recipient']) ? (int)$params['id_recipient'] : null)
                    ->setAddress($params['address'] ?? null)
                    ->setStatus($params['status'] ?? null));

            $result = $this->buildResult($foundAddresses);
            $httpCode = $this->buildHttpCode($foundAddresses);
        } else {
            $httpCode = 500;
            $result = [
                'status' => 'fail',
                'message' => $resultOfParamValidation
            ];
        }
        return $this->serverResponseFactory->createJsonResponse($httpCode, $result);
    }

    /** Подготавливает данные для ответа
     * @param array $foundAddresses
     * @return array|Address
     */
    protected function buildResult(array $foundAddresses)
    {
        $result = [];
        foreach ($foundAddresses as $foundAddress) {
            $result[] = $this->serializeAddress($foundAddress);
        }
        return $result;
    }


    /** Подготавливает http code
     * @param array $foundAddresses
     * @return int
     */
    protected function buildHttpCode(array $foundAddresses): int
    {
        return 200;
    }

    /**
     * @param SearchAddressService\AddressDto $addressDto
     * @return array
     */
    final protected function serializeAddress(AddressDto $addressDto): array
    {
        return [
            'id_address' => $addressDto->getIdAddress(),
            'id_recipient' => $addressDto->getIdRecipient(),
            'address' => $addressDto->getAddress(),
            'status' => $addressDto->getStatus()
        ];
    }
}
