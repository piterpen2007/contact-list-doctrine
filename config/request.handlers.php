<?php

use EfTech\ContactList\Controller\AddressAdministrationController;
use EfTech\ContactList\Controller\GetContactCollectionController;
use EfTech\ContactList\Controller\GetCustomersCollectionController;
use EfTech\ContactList\Controller\GetRecipientsCollectionController;

return [
    '/recipient' => GetRecipientsCollectionController::class,
    '/customers' =>GetCustomersCollectionController::class,
    '/contact' => GetContactCollectionController::class,
    '/address-add/administration' => AddressAdministrationController::class,
];
