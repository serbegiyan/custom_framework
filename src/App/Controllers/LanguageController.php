<?php

namespace App\Controllers;

use App\Interfaces\ResponseInterface;
use App\Services\Localization;
use Core\JsonResponse;
use Core\RedirectResponse;
use Core\Request;

class LanguageController
{
    public function __construct(
        public Request $request,
        public Localization $local,
    ) {
    }

    public function switchLang(): ResponseInterface
    {
        $response = new JsonResponse(['message' => 'success']);
        $redirect = new RedirectResponse('/');
        $path = $this->request->getPath();
        $params = $this->request->getParams();
        $refer = $this->request->getReferer();
        if (array_key_exists('lang', $params)) {
            $lang = $this->request->getString('lang');
            $this->local->setLang($lang, $response);
            return $response->withHeader('Location', $refer);
        }
        return $redirect;
    }
}
