<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Localization;

class LanguageController
{
    public function __construct(
        public Request $request,
        public Localization $local,
    )
    {}

    public function switchLang(): void
    {
        $path = $this->request->getPath();
        $params = $this->request->getParams();
        if(array_key_exists('lang', $params)){
            $this->local->setLang((string)$params['lang']);
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));           
        }
    }
}