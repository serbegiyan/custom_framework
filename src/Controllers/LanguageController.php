<?php

namespace App\Controllers;

use App\Core\Localization;
use App\Core\Request;

class LanguageController
{
    public function __construct(
        public Request $request,
        public Localization $local,
    ) {
    }

    public function switchLang(): void
    {
        $path = $this->request->getPath();
        $params = $this->request->getParams();
        if (array_key_exists('lang', $params)) {
            $lang = $this->request->getString('lang');
            $this->local->setLang($lang);
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        }
    }
}
