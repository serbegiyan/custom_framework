<?php

namespace Tests\Unit;

use App\Controllers\ImporterController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Exceptions\ForbiddenException;
use App\Services\ImporterService;
use App\Services\Localization;
use Core\HtmlResponse;
use Core\Request;
use Core\View;
use App\Exceptions\ValidationException;
use App\UseCases\ImportUseCase;
use App\Services\OrganizationService;
use App\ValueObjects\OrganizationId;

#[CoversClass(ImporterController::class)]
class ImporterControllerTest extends TestCase
{
    /**
     * @var \Core\Request&\PHPUnit\Framework\MockObject\MockObject
     */
    private Request $request;

    /**
     * @var \App\Services\OrganizationService&\PHPUnit\Framework\MockObject\MockObject
     */
    private OrganizationService $orgService;

    /**
     * @var \App\Services\Localization&\PHPUnit\Framework\MockObject\MockObject
     */
    private Localization $local;

    /**
     * @var \Core\View&\PHPUnit\Framework\MockObject\MockObject
     */
    private View $view;

    /**
     * @var \App\UseCases\ImportUseCase&\PHPUnit\Framework\MockObject\MockObject
     */
    private ImportUseCase $useCase;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(Request::class);
        $this->orgService = $this->createMock(OrganizationService::class);
        $this->local = $this->createMock(Localization::class);
        $this->view = $this->createMock(View::class);
        $this->useCase = $this->createMock(ImportUseCase::class);
    }

    public function testIfFileSizeInvalid(): void
    {
        $this->local->expects($this->once())->method('translate')->willReturn('error_400');
        $this->request->expects($this->once())->method('isValidSize')
            ->with('csv_file')->willReturn(false);

        $controller = new ImporterController($this->request, $this->orgService,
            $this->local, $this->view, $this->useCase);

        $this->expectException(ValidationException::class);
        $controller->store();
    }

    public function testIfFileNotExists(): void
    {
        $this->local->expects($this->once())->method('translate')->willReturn('error_400');
        $this->request->expects($this->once())->method('isValidSize')
            ->with('csv_file')->willReturn(true);
        $this->request->expects($this->once())->method('getFiles')
            ->with('csv_file')->willReturn(null);

        $controller = new ImporterController($this->request, $this->orgService,
            $this->local, $this->view, $this->useCase);

        $this->expectException(ValidationException::class);
        $controller->store();
    }

    public function testIfUserNotHasOrganization(): void
    {
        $this->local->expects($this->once())->method('translate')->willReturn('error_400');
        $this->request->expects($this->once())->method('isValidSize')
            ->with('csv_file')->willReturn(true);
        $this->request->expects($this->once())->method('getFiles')
            ->with('csv_file')->willReturn('path');
        $this->request->expects($this->once())->method('getUserId')
            ->willReturn(5);
        $this->orgService->expects($this->once())->method('getOrgId')
            ->with(5)->willReturn(null);

        $controller = new ImporterController($this->request, $this->orgService,
            $this->local, $this->view, $this->useCase);

        $this->expectException(ForbiddenException::class);
        $controller->store();
    }

    public function testIfImportStoreCorrect(): void
    {
        $this->request->expects($this->once())->method('isValidSize')
            ->with('csv_file')->willReturn(true);
        $this->request->expects($this->once())->method('getFiles')
            ->with('csv_file')->willReturn('path');
        $this->request->expects($this->once())->method('getUserId')
            ->willReturn(5);
        $orgId = new OrganizationId(1);
        $this->orgService->expects($this->once())->method('getOrgId')
            ->with(5)->willReturn($orgId);
        $this->useCase->expects($this->once())->method('runTransaction')
            ->with($orgId, 'path')
            ->willReturn(['statics' => ['USA', 'NY'], 'skippedRows' => []]);
        $this->view->expects($this->once())->method('render')
            ->with('analize', ['statics' => ['USA', 'NY'], 'skippedRows' => []])
            ->willReturn('<p>Success</p>');

        $controller = new ImporterController($this->request, $this->orgService,
        $this->local, $this->view, $this->useCase);
        $result = $controller->store();
        $code = $result->getStatusCode();
        $body = $result->getBody();

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertSame(200, $code);
        $this->assertSame('<p>Success</p>', $body);
    }
}