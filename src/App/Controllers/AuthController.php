<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\Localization;
use Core\JsonResponse;
use Core\RedirectResponse;
use Core\Request;
use Core\Session;

class AuthController
{
    public function __construct(
        public Request $request,
        public Session $session,
        public Localization $local,
        public AuthService $service,
    ) {
    }

    public function login(): JsonResponse
    {
        $email = $this->request->getString('email');
        $password = $this->request->getString('password');

        $user_id = $this->service->loginUser($email, $password);

        $this->session->set('user_id', $user_id);

        return new JsonResponse(['message' => $this->local->translate('auth.login_success')], 200);
    }

    public function register(): JsonResponse
    {
        $email = $this->request->getString('email');
        $password = $this->request->getString('password');
        $name = $this->request->getString('name');

        $user_id = $this->service->registerUser($name, $email, $password);

        $this->session->set('user_id', $user_id);

        return new JsonResponse(['message' => $this->local->translate('auth.registration_success')], 201);
    }

    public function logout(): RedirectResponse
    {
        $this->session->destroy();
        return (new RedirectResponse('/users/login'))
            ->withCookie((string)session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/'
            ]);
    }
}
