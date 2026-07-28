<?php

namespace App\Interfaces;

use App\Core\Request;
use App\Core\Session;

interface MiddlewareInterface
{
    public function handle(Request $request, Session $session): void;
}
