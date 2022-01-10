<?php

namespace EfTech\ContactList\Controller;
/**
 * Получение информации о одном клиенте
 */
class GetCustomersController extends GetCustomersCollectionController
{
    /**
     * @inheritDoc
     */
    protected function buildHttpCode(array $foundCustomers): int
    {
        return 0 === count($foundCustomers) ? 404 : 200;
    }

    /**
     * @inheritDoc
     */
    protected function buildResult(array $foundCustomers)
    {
        return 1 === count($foundCustomers)
            ? $this->serializeCustomer(current($foundCustomers))
            : [ 'status' => 'fail', 'message' => 'entity not found'];

    }

}