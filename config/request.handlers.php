<?php

use EfTech\ContactList\Controller\FindContactOnCategory;
use EfTech\ContactList\Controller\FindCustomers;
use EfTech\ContactList\Controller\FindRecipient;
use EfTech\ContactList\Infrastructure\AppConfig;
use EfTech\ContactList\Infrastructure\http\ServerRequest;
use EfTech\ContactList\Infrastructure\Logger\LoggerInterface;

return [
    '/recipient' => static function(ServerRequest $serverRequest,LoggerInterface $logger, AppConfig $appConfig) {
        return (new FindRecipient($logger,$appConfig))($serverRequest);
        },
    '/customers' => static function(ServerRequest $serverRequest,LoggerInterface $logger, AppConfig $appConfig) {
        return (new FindCustomers($logger,$appConfig))($serverRequest);
    },
    '/category' => static function(ServerRequest $serverRequest,LoggerInterface $logger, AppConfig $appConfig) {
        return (new FindContactOnCategory($logger,$appConfig))($serverRequest);
    }
];
