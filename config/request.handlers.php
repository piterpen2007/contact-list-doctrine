<?php

use EfTech\ContactList\Controller\FindContactOnCategory;
use EfTech\ContactList\Controller\FindCustomers;
use EfTech\ContactList\Controller\FindRecipient;

return [
    '/recipient' => FindRecipient::class,
    '/customers' =>FindCustomers::class,
    '/category' => FindContactOnCategory::class
];
