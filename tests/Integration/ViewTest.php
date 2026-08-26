<?php

namespace Tests\Integration;

use Core\View;
use App\Models\Statics;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use InvalidArgumentException;

#[CoversClass(View::class)]
class ViewTest extends TestCase
{
    public function testIfRenderFileCorrect(): void
    {
        $statics = new Statics();
        $statics->country = 'TestCountry';
                        
        $data = ['statics' => [$statics]];

        $view = new View();

        $result = $view->render('analize', $data);

        $this->assertStringContainsString('TestCountry', $result);
    }

    public function testIfRenderFileIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $view = new View();
        $view->render('wrongTemplate');
    }
}