<?php

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Session;
use App\Interfaces\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Session $session): void
    {
        $user_id = $session->get('user_id');
        if (!$user_id) {
            header('Location: /users/login');
            exit;
        }
    }
}
