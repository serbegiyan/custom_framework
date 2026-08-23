<?php

namespace App\Controllers;

use App\Services\Localization;
use Core\Request;
use App\Interfaces\ResponseInterface;
use Core\JsonResponse;

class LanguageController
{
    public function __construct(
        public Request $request,
        public Localization $local,
    ) {
    }

    public function switchLang(): void
    {
        $response = new JsonResponse(['message' => 'success']);
        $path = $this->request->getPath();
        $params = $this->request->getParams();
        if (array_key_exists('lang', $params)) {
            $lang = $this->request->getString('lang');
            $this->local->setLang($lang, $response);
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        }
    }
}
