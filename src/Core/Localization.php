<?php

namespace App\Core;

class Localization
{
    public const LANG = ['ru', 'en'];

    private string $currentLang;
    private array $ruDic;
    private array $enDic;

    public function __construct(   
    )
    {
        $cook = $_COOKIE['app_lang'] ?? '';      
        $this->currentLang = (is_string($cook) && in_array($cook, self::LANG, true) ? $cook : self::LANG[0]); 
        $this->ruDic = require __DIR__ . '/../Lang/ru.php';     
        $this->enDic = require __DIR__ . '/../Lang/en.php';  
    }

    private function getVocabulary(): array
    {        
        if($this->currentLang === 'ru'){
            return $this->ruDic;
        }       
        return $this->enDic;              
    }

    public function translate(string $key): string
    {
        /** @var array<string, string> */
        $vocabulary = $this->getVocabulary();
        if(! array_key_exists($key, $vocabulary)){
            return $key;
        }
        return $vocabulary[$key];
    }

    public function setLang(string $lang): void
    {
        if(in_array($lang, self::LANG)){
            setcookie('app_lang', $lang, [ 'expires' => time() + 31536000, 'path' => '/' ]);
            $this->currentLang = $lang;
        }
    }

}