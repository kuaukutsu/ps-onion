<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\SimpleCache\CacheInterface;
use Ramsey\Uuid\Rfc4122\Validator;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\Validator\ValidatorInterface;
use kuaukutsu\ps\onion\application\decorator\CacheDecorator;

use function DI\create;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

return [
    RequestFactoryInterface::class => create(HttpFactory::class),
    StreamFactoryInterface::class => create(HttpFactory::class),
    UuidFactoryInterface::class => create(UuidFactory::class),
    ValidatorInterface::class => create(Validator::class),
    CacheInterface::class => create(CacheDecorator::class),
];
