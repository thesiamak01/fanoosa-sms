<?php

namespace Fanoosa\Sms\Exceptions;

use Exception;

class InvalidConfigurationException extends Exception
{
    // Exception for invalid configuration
}

class SmsSendingFailedException extends Exception
{
    // Exception for SMS sending failures
}
