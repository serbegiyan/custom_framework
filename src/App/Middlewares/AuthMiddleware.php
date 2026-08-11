<?php

namespace App\Middlewares;

use App\Interfaces\MiddlewareInterface;
use Core\Request;
use Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Session $session): void
    {
        $user_id = $session->get('user_id');
        if (!$user_id) {
            header('Location: /users/login');
            exit;
        }
        $request->setUserId((int)$user_id);
    }
}
