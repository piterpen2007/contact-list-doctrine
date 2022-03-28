<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Service\ArrivalAddressService;
use EfTech\ContactList\Service\ArrivalNewAddressService\NewAddressDto;
use EfTech\ContactList\Service\ArrivalNewAddressService\ResultRegisterNewAddressDto;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateAddressController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
    private ArrivalAddressService $addressService;
    /**
     * Соединение с БД
     *
     * @var ConnectionInterface
     */
    private ConnectionInterface $connection;


    /**
     * @param ArrivalAddressService $addressService
     * @param ServerResponseFactory $serverResponseFactory
     * @param ConnectionInterface $connection
     */
    public function __construct(
        ArrivalAddressService $addressService,
        \EfTech\ContactList\Infrastructure\http\ServerResponseFactory $serverResponseFactory,
        \EfTech\ContactList\Infrastructure\Db\ConnectionInterface $connection
    ) {
        $this->addressService = $addressService;
        $this->serverResponseFactory = $serverResponseFactory;
        $this->connection = $connection;
    }


    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->connection->beginTransaction();
            $requestData = json_decode($request->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $validationResult = $this->validateData($requestData);

            if (0 === count($validationResult)) {
                // Создаю dto с входными данными
                $responseDto = $this->runService($requestData);
                $httpCode = 201;
                $jsonData = $this->buildJsonData($responseDto);
            } else {
                $httpCode = 400;
                $jsonData = ['status' => 'fail','message' => implode('.', $validationResult)];
            }
            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollback();
            $httpCode = 500;
            $jsonData = ['status' => 'fail','message' => $e->getMessage()];
        }

        return $this->serverResponseFactory->createJsonResponse($httpCode, $jsonData);
    }

    private function runService(array $requestData): ResultRegisterNewAddressDto
    {
        $requestDto = new NewAddressDto(
            $requestData['id_recipient'],
            $requestData['address'],
            $requestData['status']
        );

        return $this->addressService->registerAddress($requestDto);
    }

    /** Формирует результаты для ответа на основе dto
     * @param ResultRegisterNewAddressDto $responseDto
     * @return array
     */
    private function buildJsonData(ResultRegisterNewAddressDto $responseDto): array
    {
        return [
            'id_address' => $responseDto->getIdAddress(),
            'id_recipient' => $responseDto->getIdRecipient(),
            'address' => $responseDto->getAddress(),
            'status' => $responseDto->getStatus()
        ];
    }

    /** Валидирует входные данные
     * @param $requestData
     * @return array
     */
    private function validateData($requestData): array
    {
        $err = [];
        if (false === is_array($requestData)) {
            $err[] = 'Данные о новом адресе не являются масивом';
        } else {
            if (false === array_key_exists('address', $requestData)) {
                $err[] = 'Отсутствует информация о адресе';
            } elseif (false === is_string($requestData['address'])) {
                $err[] = 'адрес должен быть строкой';
            } elseif ('' === trim($requestData['address'])) {
                $err[] = 'адрес не может быть пустой строкой';
            }

            if (false === array_key_exists('status', $requestData)) {
                $err[] = 'Отсутствует информация о статусе';
            } elseif (false === is_string($requestData['status'])) {
                $err[] = 'статус должен быть строкой';
            } elseif ('' === trim($requestData['status'])) {
                $err[] = 'статус не может быть пустой строкой';
            }

            if (false === array_key_exists('id_recipient', $requestData)) {
                $err[] = 'Отсутствует информация о id контакта';
            }
        }

        return $err;
    }
}
