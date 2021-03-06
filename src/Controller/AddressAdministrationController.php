<?php

namespace EfTech\ContactList\Controller;

use Doctrine\ORM\EntityManagerInterface;
use EfTech\ContactList\Infrastructure\Auth\HttpAuthProvider;
use EfTech\ContactList\Exception\RuntimeException;
use EfTech\ContactList\Infrastructure\Controller\ControllerInterface;
use EfTech\ContactList\Infrastructure\Db\ConnectionInterface;
use EfTech\ContactList\Infrastructure\http\ServerResponseFactory;
use EfTech\ContactList\Service\SearchColleagueService;
use EfTech\ContactList\Service\SearchColleagueService\SearchColleagueCriteria;
use EfTech\ContactList\Service\SearchCustomersService;
use EfTech\ContactList\Service\SearchCustomersService\SearchCustomersCriteria;
use EfTech\ContactList\Service\SearchKinsfolkService;
use EfTech\ContactList\Service\SearchKinsfolkService\SearchKinsfolkCriteria;
use EfTech\ContactList\Service\SearchRecipientsService;
use EfTech\ContactList\Service\SearchRecipientsService\SearchRecipientsCriteria;
use Psr\Log\LoggerInterface;
use EfTech\ContactList\Infrastructure\ViewTemplate\ViewTemplateInterface;
use EfTech\ContactList\Service\ArrivalAddressService;
use EfTech\ContactList\Service\ArrivalNewAddressService\NewAddressDto;
use EfTech\ContactList\Service\SearchAddressService;
use EfTech\ContactList\Service\SearchAddressService\SearchAddressCriteria;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class AddressAdministrationController implements ControllerInterface
{
    private ServerResponseFactory $serverResponseFactory;
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
    private SearchColleagueService $searchColleagueService;
    private SearchKinsfolkService $searchKinsfolkService;
    private SearchRecipientsService $searchRecipientsService;
    private SearchCustomersService $searchCustomersService;

    /**
     * Менеджер сущностей
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;


    /**
     * @param ArrivalAddressService $arrivalAddressService
     * @param SearchAddressService $addressService
     * @param ViewTemplateInterface $viewTemplate
     * @param LoggerInterface $logger
     * @param HttpAuthProvider $httpAuthProvider
     * @param ServerResponseFactory $serverResponseFactory
     * @param SearchColleagueService $searchColleagueService
     * @param SearchKinsfolkService $searchKinsfolkService
     * @param SearchRecipientsService $searchRecipientsService
     * @param SearchCustomersService $searchCustomersService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ArrivalAddressService $arrivalAddressService,
        SearchAddressService $addressService,
        ViewTemplateInterface $viewTemplate,
        LoggerInterface $logger,
        HttpAuthProvider $httpAuthProvider,
        \EfTech\ContactList\Infrastructure\http\ServerResponseFactory $serverResponseFactory,
        \EfTech\ContactList\Service\SearchColleagueService $searchColleagueService,
        \EfTech\ContactList\Service\SearchKinsfolkService $searchKinsfolkService,
        \EfTech\ContactList\Service\SearchRecipientsService $searchRecipientsService,
        \EfTech\ContactList\Service\SearchCustomersService $searchCustomersService,
        \Doctrine\ORM\EntityManagerInterface $em
    ) {
        $this->arrivalAddressService = $arrivalAddressService;
        $this->addressService = $addressService;
        $this->viewTemplate = $viewTemplate;
        $this->logger = $logger;
        //$this->searchContactsService = $searchContactsService;
        $this->httpAuthProvider = $httpAuthProvider;
        $this->serverResponseFactory = $serverResponseFactory;
        $this->searchColleagueService = $searchColleagueService;
        $this->searchKinsfolkService = $searchKinsfolkService;
        $this->searchRecipientsService = $searchRecipientsService;
        $this->searchCustomersService = $searchCustomersService;
        $this->em = $em;
    }


    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            if (false === $this->httpAuthProvider->isAuth()) {
                return $this->httpAuthProvider->doAuth($request->getUri());
            }
            $this->logger->info('run AddressAdministrationController::__invoke');

            $resultCreationAddress = [];
            if ('POST' === $request->getMethod()) {
                $resultCreationAddress = $this->creationOfAddress($request);
            }
            $dtoAddressesCollection = $this->addressService->search(new SearchAddressCriteria());
            $recipients = $this->searchRecipientsService->search(new SearchRecipientsCriteria());
            $colleagues = $this->searchColleagueService->search(new SearchColleagueCriteria());
            $customers = $this->searchCustomersService->search(new SearchCustomersCriteria());
            $kinsfolk = $this->searchKinsfolkService->search(new SearchKinsfolkCriteria());
            $dtoContactsCollection = array_merge($recipients, $colleagues, $customers, $kinsfolk);
            $viewData = [
                'Addresses' => $dtoAddressesCollection,
                'contacts' => $dtoContactsCollection
            ];
            $context = array_merge($viewData, $resultCreationAddress);
            $template = 'address.administration.twig';
            $httpCode = 200;
        } catch (Throwable $e) {
            $httpCode = 500;
            $template = 'errors.twig';
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

        return $this->serverResponseFactory->createHtmlResponse($httpCode, $html);
    }

    /** Результат создания адресов
     *
     * @param ServerRequestInterface $request
     * @return array - данные о ошибках у форм создания адресов
     */
    private function creationOfAddress(ServerRequestInterface $request): array
    {
        $dataToCreate = [];
        parse_str($request->getBody(), $dataToCreate);

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
    private function validateAddresses(array $dataToCreate): array
    {
        $errs = [];

        $errAddress = $this->validateAddress($dataToCreate);
        if (count($errAddress) > 0) {
            $errs = array_merge($errs, $errAddress);
        }
        $this->validateIdRecipient($dataToCreate);
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
    private function validateAddress(array $dataToCreate): array
    {
        $errs = [];

        if (false === array_key_exists('address', $dataToCreate)) {
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


    private function validateIdRecipient(array $dataToCreate): void
    {
        if (false === array_key_exists('id_recipient', $dataToCreate)) {
            throw new RuntimeException('Нет данных о id контакта');
        } elseif (false === is_array($dataToCreate['id_recipient'])) {
            throw new RuntimeException('Данные о коонтактах должны быть массивом');
        }
    }

    /** Валидация статуса
     * @param array $dataToCreate
     * @return array
     */
    private function validateStatus(array $dataToCreate): array
    {
        $errs = [];

        if (false === array_key_exists('status', $dataToCreate)) {
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
        try {
            $this->em->beginTransaction();

            $this->arrivalAddressService->registerAddress(
                new NewAddressDto(
                    $dataToCreate['id_recipient'],
                    $dataToCreate['address'],
                    $dataToCreate['status']
                )
            );
            $this->em->flush();
            $this->em->commit();
        } catch (Throwable $e) {
            $this->em->rollback();

            throw new RuntimeException(
                'Ошибка при добавлении нового адреса: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
