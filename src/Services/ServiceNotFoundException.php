<?php

namespace App\Services;

use App\Core\Interfaces\NotFoundExceptionInterface;
use Exception;

class ServiceNotFoundException extends Exception implements NotFoundExceptionInterface
{
}
