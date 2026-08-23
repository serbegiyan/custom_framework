<?php

namespace Tests\Unit;

use App\Controllers\AnalizeController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Exceptions\ForbiddenException;
use App\Interfaces\DatabaseInterface;
use App\Services\AnalizerService;
use App\Services\Localization;
use App\Services\OrganizationService;
use Core\HtmlResponse;
use Core\Request;
use Core\View;
use App\ValueObjects\OrganizationId;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[CoversClass(AnalizeController::class)]
class AnalizeControllerTest extends TestCase
{
    private DatabaseInterface $db;
    private Request $request;
    private AnalizerService $analizer;
    private Localization $local;
    private OrganizationService $orgService;
    private View $view;

    public function setUp(): void
    {
        parent::setUp();
        $this->db = $this->createStub(DatabaseInterface::class);
        $this->request = $this->createStub(Request::class);
        $this->analizer = $this->createMock(AnalizerService::class);
        $this->local = $this->createStub(Localization::class);
        $this->orgService = $this->createMock(OrganizationService::class);
        $this->view = $this->createMock(View::class);
    }

    public function testIfIndexRunCorrect(): void
    {
        $orgId = new OrganizationId(2);
        $this->request->method('getUserId')->willReturn(1);
        $this->orgService->method('getOrgId')->with(1)->willReturn($orgId);
        $this->request->method('getParams')->willReturn([]);
        $data = [
            'USA', 'NY', '1', 'male', '1', 'married', 10000, '2000-12-12', '2020-06-06', 2
        ];
        $this->analizer->method('run')->with([], $orgId)->willReturn($data);
        $this->view->method('render')
            ->with('analize', ['statics' => $data,])
            ->willReturn('<h1>Analize Page</h1>');
        $controller = new AnalizeController(
            $this->db, $this->request, $this->analizer, $this->local, 
            $this->orgService, $this->view
        );

        $result = $controller->index();

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame("<h1>Analize Page</h1>", $result->getBody());
    }
    
    #[AllowMockObjectsWithoutExpectations]
    public function testIfAccessForbidden(): void
    {
        $this->request->method('getUserId')->willReturn(1);
        $this->orgService->method('getOrgId')->with(1)->willReturn(null);
        $controller = new AnalizeController(
            $this->db, $this->request, $this->analizer, $this->local, 
            $this->orgService, $this->view
        );
        $this->expectException(ForbiddenException::class);
        $result = $controller->index();
    }
}