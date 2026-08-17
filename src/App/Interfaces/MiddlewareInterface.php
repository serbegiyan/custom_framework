<?php

namespace App\Interfaces;

use Core\Request;
use Core\Session;

interface MiddlewareInterface
{
    public function handle(Request $request, Session $session): ResponseInterface;
}
