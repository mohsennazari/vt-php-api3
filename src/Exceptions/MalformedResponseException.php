<?php

namespace Monaz\VirusTotal\Exceptions;

use Exception;

class MalformedResponseException extends Exception
{
    protected $message = "The response body is not formed as it is should be.";
}
