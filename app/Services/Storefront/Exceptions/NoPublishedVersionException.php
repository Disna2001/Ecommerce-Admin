<?php

namespace App\Services\Storefront\Exceptions;

use Exception;

class NoPublishedVersionException extends Exception
{
    protected $message = 'No published version exists for this page.';
}
