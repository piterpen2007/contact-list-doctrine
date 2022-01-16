<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Entity\Address;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\Validator\Assert;
use EfTech\ContactList\Service\SearchAddressService;
use EfTech\ContactList\Service\SearchAddressService\AddressDto;

class GetAddressCollectionController implements ControllerInterface
{
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
     */
    public function __construct(LoggerInterface $logger, SearchAddressService $contactListService)
    {
        $this->logger = $logger;
        $this->contactListService = $contactListService;
    }

    /**  Валдирует параматры запроса
     * @param ServerRequest $request
     * @return string|null
     */
    private function validateQueryParams(ServerRequest $request):?string
    {
        $paramTypeValidation = [
            'id_address' => "incorrect id_address",
            'id_recipient' => 'incorrect id_recipient',
            'address' => 'incorrect address',
            'status' => 'incorrect status'
        ];
        $queryParams = array_merge($request->getQueryParams(),$request->getAttributes());
        return Assert::arrayElementsIsString($paramTypeValidation,$queryParams);
    }

    public function __invoke(ServerRequest $request): httpResponse
    {
        $this->logger->log("Ветка contact-list");
        $resultOfParamValidation = $this->validateQueryParams($request);

        if (null === $resultOfParamValidation) {
            $params = array_merge($request->getQueryParams(), $request->getAttributes());
            $foundAddresses = $this->contactListService
                ->search((new SearchAddressService\SearchAddressCriteria())
                    ->setIdAddress($params['id_address'] ?? null)
                    ->setIdRecipient($params['id_recipient'] ?? null)
                    ->setAddress($params['address'] ?? null)
                    ->setStatus($params['status'] ?? null)
                );

            $result = $this->buildResult($foundAddresses);
            $httpCode = $this->buildHttpCode($foundAddresses);


        } else {
            $httpCode = 500;
            $result=[
                'status' => 'fail',
                'message' => $resultOfParamValidation
            ];
        }
        return ServerResponseFactory::createJsonResponse($httpCode,$result);
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
    protected function buildHttpCode(array $foundAddresses):int
    {
        return 200;
    }

    /**
     * @param SearchAddressService\AddressDto $addressDto
     * @return array
     */
    final protected function serializeAddress(AddressDto $addressDto):array
    {
        return [
            'id_address' => $addressDto->getIdAddress(),
            'id_recipient' => $addressDto->getIdRecipient(),
            'address' => $addressDto->getAddress(),
            'status' => $addressDto->getStatus()
        ];
    }

}