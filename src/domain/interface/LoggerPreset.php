<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

/**
 * @example LoggerInterface $logger
 * ```php
 * $logger->preset(
 *      new LoggerExceptionPreset($exception, $context),
 *      __METHOD__,
 * )
 *
 * $logger->preset(
 *      new LoggerHttpClientPreset($request, $response, $context),
 *      '\kuaukutsu\ps\onion\infrastructure\http\HttpClient::send',
 *  )
 *
 * $logger->preset(
 *      new LoggerTracePreset($message, $context),
 * )
 * ```
 */
interface LoggerPreset
{
    public function getLevel(): LoggerLevel;

    public function getMessage(): string;

    public function getContext(): array;
}
