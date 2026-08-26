<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\Localization;
use Core\JsonResponse;
use Core\Request;

#[CoversClass(Localization::class)]
class LocalizationTest extends TestCase
{
    public function testIfLangSetCorrectly(): void
    {
        $response = new JsonResponse(['message' => 'success']);
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getCookies')
            ->with('app_lang')
            ->willReturn('ru');
        $local = new Localization($request);
        $local->setLang('en', $response);
        $cookies = $response->getCookies();
        $this->assertArrayHasKey('app_lang', $cookies);
        $this->assertSame('en', $cookies['app_lang']['value']);
    }

    public function testIfLangSaveCorrectAndSwich(): void
    {
        $response = new JsonResponse(['message' => 'success']);
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getCookies')
            ->with('app_lang')
            ->willReturn('en');
        $local = new Localization($request);
        $local->setLangDirPath(__DIR__ . '/../Stubs');

        $lang = $local->getCurrentLang();
        $this->assertSame('en', $lang);

        $word = $local->translate('created');
        $this->assertSame('created', $word);
    }

    public function testIfLangIncorrect(): void
    {
        $response = new JsonResponse(['message' => 'success']);
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getCookies')
            ->with('app_lang')
            ->willReturn('fr');
        $local = new Localization($request);
        $lang = $local->getCurrentLang();
        $this->assertSame('ru', $lang);
    }

    public function testIfTranslateRunСorrect(): void
    {
        $response = new JsonResponse(['message' => 'success']);
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getCookies')
            ->with('app_lang')
            ->willReturn('ru');
        $local = new Localization($request);
        $local->setLangDirPath(__DIR__ . '/../Stubs');

        $phrase = $local->translate('created');
        $this->assertSame('Успешно создано', $phrase);
    }    

    public function testIfTranslateWithoutKey(): void
    {
        $response = new JsonResponse(['message' => 'success']);
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getCookies')
            ->with('app_lang')
            ->willReturn('ru');
        $local = new Localization($request);
        $local->setLangDirPath(__DIR__ . '/../Stubs');

        $phrase = $local->translate('createdby');
        $this->assertSame('createdby', $phrase);
    }    
}