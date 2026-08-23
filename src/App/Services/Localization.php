<?php

namespace App\Services;

use App\Interfaces\ResponseInterface;
use Core\Request;

class Localization
{
    public const LANG = ['ru', 'en'];

    private string $currentLang;

    private string $langDirPath;


    public function __construct(
        public Request $request,
    ) {
        $this->langDirPath = __DIR__ . '/../../../lang';
        $cook = $this->request->getCookies('app_lang');
        $this->currentLang = (is_string($cook) && in_array($cook, self::LANG, true) ? $cook : self::LANG[0]);
    }

    /**
     * @return array<string, string>
     */
    private function getVocabulary(): array
    {
        if ($this->currentLang === 'ru') {
            return require $this->langDirPath . '/ru.php';
        }
        return require $this->langDirPath . '/en.php';
    }

    public function translate(string $key): string
    {
        /** @var array<string, string> */
        $vocabulary = $this->getVocabulary();
        if (! array_key_exists($key, $vocabulary)) {
            return $key;
        }
        return $vocabulary[$key];
    }

    public function setLang(string $lang, ResponseInterface $response): void
    {
        if (in_array($lang, self::LANG)) {
            $response->withCookie('app_lang', $lang, [ 'expires' => time() + 31536000, 'path' => '/' ]);
            $this->currentLang = $lang;
        }
    }

    public function setLangDirPath(string $path): void
    {
        $this->langDirPath = $path;
    }

    public function getCurrentLang(): string
    {
        return $this->currentLang;
    }
}
