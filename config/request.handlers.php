<?php
return [
    '/recipient' => include __DIR__ . '/../src/Controller/findRecipient.handler.php',
    '/customers' => include __DIR__ . '/../src/Controller/findCustomers.handler.php',
    '/category' => include __DIR__ . '/../src/Controller/findContactOnCategory.handler.php'
];
