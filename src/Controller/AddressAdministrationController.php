<?php

namespace EfTech\ContactList\Controller;

use EfTech\ContactList\Infrastructure\Auth\HttpAuthProvider;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\http\httpResponse;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;
use EfTech\ContactList\Infrastructure\ViewTemplate\ViewTemplateInterface;
use EfTech\ContactList\Service\ArrivalAddressService;
use EfTech\ContactList\Service\ArrivalNewAddressService\NewAddressDto;
use EfTech\ContactList\Service\SearchAddressService;
use EfTech\ContactList\Service\SearchAddressService\SearchAddressCriteria;
use EfTech\ContactList\Service\SearchContactsService;
use EfTech\ContactList\Service\SearchContactsService\SearchContactsCriteria;

class AddressAdministrationController implements ControllerInterface
{
    private HttpAuthProvider $httpAuthProvider;
    /** Сервис добавлениянового адреса
     * @var ArrivalAddressService
     */
    private ArrivalAddressService $arrivalAddressService;
    /** Сервис поиска адресов
     * @var SearchAddressService
     */
    private SearchAddressService $addressService;
    /** шаблонизатор для рендеринга html
     * @var ViewTemplateInterface
     */
    private ViewTemplateInterface $viewTemplate;
    /** Логер
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    private SearchContactsService $searchContactsService;

    /**
     * @param ArrivalAddressService $arrivalAddressService
     * @param SearchAddressService $addressService
     * @param ViewTemplateInterface $viewTemplate
     * @param LoggerInterface $logger
     * @param SearchContactsService $searchContactsService
     * @param HttpAuthProvider $httpAuthProvider
     */
    public function __construct(
        ArrivalAddressService $arrivalAddressService,
        SearchAddressService $addressService,
        ViewTemplateInterface $viewTemplate,
        LoggerInterface $logger,
        SearchContactsService $searchContactsService,
        HttpAuthProvider $httpAuthProvider

    ) {
        $this->arrivalAddressService = $arrivalAddressService;
        $this->addressService = $addressService;
        $this->viewTemplate = $viewTemplate;
        $this->logger = $logger;
        $this->searchContactsService = $searchContactsService;
        $this->httpAuthProvider = $httpAuthProvider;
    }


    public function __invoke(ServerRequest $request): httpResponse
    {
        try {
            if (false === $this->httpAuthProvider->isAuth()) {
                return $this->httpAuthProvider->doAuth($request->getUri());
            }
            $this->logger->log('run AddressAdministrationController::__invoke');

            $resultCreationAddress = [];
            if ('POST' === $request->getMethod()) {
                $resultCreationAddress = $this->creationOfAddress($request);
            }
            $dtoAddressesCollection = $this->addressService->search(new SearchAddressCriteria());
            $dtoContactsCollection = $this->searchContactsService->search(new SearchContactsCriteria());
            $viewData = [
                'Addresses' => $dtoAddressesCollection,
                'contacts' => $dtoContactsCollection
            ];
            $context = array_merge($viewData, $resultCreationAddress);
            $template = __DIR__ . '/../../templates/address.administration.phtml';
            $httpCode = 200;
        } catch (\Throwable $e) {
            $httpCode = 500;
            $template = __DIR__ . '/../../templates/errors.phtml';
            $context = [
                'errors' => [
                    $e->getMessage()
                ]
            ];
        }
        $html = $this->viewTemplate->render(
            $template,
            $context
        );

        return ServerResponseFactory::createHtmlResponse($httpCode,$html);
    }

    /** Результат создания адресов
     *
     * @param ServerRequest $request
     * @return array - данные о ошибках у форм создания адресов
     */
    private function creationOfAddress(ServerRequest $request):array
    {
        $dataToCreate = [];
        parse_str($request->getBody(),$dataToCreate);

        $result = [
            'formValidationResults' => [
                'address' => [],
            ]
        ];
        $result['formValidationResults']['address'] = $this->validateAddresses($dataToCreate);
        if (0 === count($result['formValidationResults']['address'])) {
            $this->createAddress($dataToCreate);
        } else {
            $result['addressData'] = $dataToCreate;
        }
        return $result;
    }


    /** Логика валидации данных адреса
     * @param array $dataToCreate
     * @return void
     */
    private function validateAddresses(array $dataToCreate):array
    {
        $errs = [];

        $errAddress = $this->validateAddress($dataToCreate);
        if (count($errAddress) > 0) {
            $errs = array_merge($errs, $errAddress);
        }

        $errIdRecipient = $this->validateIdRecipient($dataToCreate);
        if (count($errIdRecipient) > 0) {
            $errs = array_merge($errs, $errIdRecipient);
        }

        $errStatus = $this->validateStatus($dataToCreate);
        if (count($errStatus) > 0) {
            $errs = array_merge($errs, $errStatus);
        }

        return $errs;
    }

    /** Валидация адреса
     * @param array $dataToCreate
     * @return array
     */
    private function validateAddress(array $dataToCreate):array
    {
        $errs = [];

        if (false === array_key_exists('address',$dataToCreate)) {
            throw new RuntimeException('Нет данных о адресе');
        } elseif (false === is_string($dataToCreate['address'])) {
            throw new RuntimeException('Данные о адресе должны быть строкой');
        } else {
            $addressLength = strlen(trim($dataToCreate['address']));
            $errAddress = [];
            if ($addressLength > 250) {
                $errAddress[] = 'адрес не может быть длинее 250 символов';
            } elseif (0 === $addressLength) {
                $errAddress[] = 'адрес не может быть пустым';
            }

            if (0 !== count($errAddress)) {
                $errs['address'] = $errAddress;
            }
        }
        return $errs;
    }


    private function validateIdRecipient(array $dataToCreate): array
    {
        $errs = [];
        if (false === array_key_exists('id_recipient',$dataToCreate)) {
            throw new RuntimeException('Нет данных о id контакта');
        } elseif (false === is_string($dataToCreate['id_recipient'])) {
            throw new RuntimeException('Данные о id контакта должны быть строкой');
        } else {
            $idRecipientNumber = trim($dataToCreate['id_recipient']);
            $idRecipientIsValid = 1 === preg_match('/^\d+$/',$idRecipientNumber);

            $errsIdRecipient = [];
            if (false === $idRecipientIsValid) {
                $errsIdRecipient[] = 'id контакта должен быть числом';
            }
            if (0 !== count($errsIdRecipient)) {
                $errs['id_recipient'] = $errsIdRecipient;
            }
        }
        return $errs;
    }

    /** Валидация статуса
     * @param array $dataToCreate
     * @return array
     */
    private function validateStatus(array $dataToCreate):array
    {
        $errs = [];

        if (false === array_key_exists('status',$dataToCreate)) {
            throw new RuntimeException('Нет данных о статусе');
        } elseif (false === is_string($dataToCreate['status'])) {
            throw new RuntimeException('Данные о статусе должны быть строкой');
        } else {
            $statusLength = strlen(trim($dataToCreate['status']));
            $errStatus = [];
            if ($statusLength > 250) {
                $errStatus[] = 'статус не может быть длинее 250 символов';
            } elseif (0 === $statusLength) {
                $errStatus[] = 'статус не может быть пустым';
            }

            if (0 !== count($errStatus)) {
                $errs['status'] = $errStatus;
            }
        }
        return $errs;
    }

    /** Создаёт адрес
     * @param array $dataToCreate
     * @return void
     */
    private function createAddress(array $dataToCreate): void
    {
        $this->arrivalAddressService->registerAddress(
            new NewAddressDto(
                (int)$dataToCreate['id_recipient'],
                $dataToCreate['address'],
                $dataToCreate['status']
            )
        );
    }
}