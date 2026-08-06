<?php

namespace App\Services;

use App\Interfaces\NotFoundExceptionInterface;
use Exception;

class ServiceNotFoundException extends Exception implements NotFoundExceptionInterface
{
}
