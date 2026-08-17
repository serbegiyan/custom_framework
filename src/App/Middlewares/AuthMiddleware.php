<?php

namespace App\Middlewares;

use App\Exceptions\UnauthorizedException;
use App\Interfaces\MiddlewareInterface;
use App\Interfaces\ResponseInterface;
use Core\JsonResponse;
use Core\Request;
use Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Session $session): ResponseInterface
    {
        $user_id = $session->get('user_id');
        if (!$user_id) {
            throw new UnauthorizedException('Unauthorized');
        }
        $request->setUserId((int)$user_id);
        return new JsonResponse(['message' => 'success'], 200);
    }
}
