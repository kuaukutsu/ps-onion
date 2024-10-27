<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Psr\Log\LogLevel;

enum LoggerLevel: string
{
    case EMERGENCY = LogLevel::EMERGENCY;
    case ALERT = LogLevel::ALERT;
    case CRITICAL = LogLevel::CRITICAL;
    case ERROR = LogLevel::ERROR;
    case WARNING = LogLevel::WARNING;
    case NOTICE = LogLevel::NOTICE;
    case INFO = LogLevel::INFO;
    case DEBUG = LogLevel::DEBUG;
}
