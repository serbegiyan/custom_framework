<?php

namespace Tests\Unit;

use App\Controllers\OrganizationController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\Localization;
use Core\Request;
use Core\JsonResponse;
use App\Services\OrganizationService;
use App\Services\Gate;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\ValueObjects\OrganizationId;
use App\Policies\OrganizationPolicy;

#[CoversClass(OrganizationController::class)]
class OrganizationControllerTest extends TestCase
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
     * @var \App\Services\Gate&\PHPUnit\Framework\MockObject\MockObject
     */
    private Gate $gate;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(Request::class);
        $this->local = $this->createMock(Localization::class);
        $this->orgService = $this->createMock(OrganizationService::class);
        $this->gate = $this->createMock(Gate::class);
    }

    public function testIfIndexRunCorrect(): void
    {        
        $this->request->expects($this->once())
            ->method('getUserId')->willReturn(5);
        $this->orgService->expects($this->once())
            ->method('getOrgList')->with(5)->willReturn(['name' => 'EPAM']);

        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);
        $result = $controller->index();
        $code = $result->getStatusCode();
        $body = $result->getBody();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $code);
        $this->assertSame('{"name":"EPAM"}', $body);
    }

    public function testIfNotExistOrganizations(): void
    {        
        $this->request->expects($this->once())
            ->method('getUserId')->willReturn(5);
        $this->orgService->expects($this->once())
            ->method('getOrgList')->with(5)->willReturn([]);

        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);
        $result = $controller->index();
        $code = $result->getStatusCode();
        $body = $result->getBody();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $code);
        $this->assertSame('[]', $body);
    }

    public function testIfStoreCorrect(): void
    {
        $this->request->expects($this->once())
            ->method('getUserId')->willReturn(5);
        $this->request->expects($this->once())
            ->method('getString')->with('name')->willReturn('EPAM');
        $this->orgService->expects($this->once())
            ->method('storeToDb')->with('EPAM', 5);        

        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);
        $result = $controller->store();
        $code = $result->getStatusCode();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $code);
    }

    public function testIfUserUnauthentificated(): void
    {
        $this->expectException(ForbiddenException::class);
        $this->request->expects($this->once())
            ->method('getUserId')->willReturn(null);        
        
        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $controller->store();     
    }

    public function testIfOrganizationNameInvalidWithStore(): void
    {
        $this->expectException(ValidationException::class);
        $this->request->expects($this->once())
            ->method('getUserId')->willReturn(5);        
        $this->request->expects($this->once())
            ->method('getString')->with('name')->willReturn('');

        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $controller->store();     
    }

    public function testIfUpdateRunCorrect(): void
    {
        $this->request->expects($this->once())
            ->method('getParams')->willReturn(['id' => 3]);
        $org_id = new OrganizationId(3);
        $this->gate->authorize('TestClass', 'update', $org_id);
        $this->request->expects($this->once())
            ->method('getString')->with('name')->willReturn('NewEPAM');
        $this->orgService->expects($this->once())
            ->method('updateToBd')->with('NewEPAM', $org_id->orgId); 
        
        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $result = $controller->update(); 
        $code = $result->getStatusCode();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $code);
    }

    public function testIfOrganizationNameInvalidWithUpdate(): void
    {
        $this->request->expects($this->once())
            ->method('getParams')->willReturn(['id' => 3]);
        $org_id = new OrganizationId(3);
        $this->gate->expects($this->once())
            ->method('authorize')->with(OrganizationPolicy::class, 'update', $org_id);        
        $this->request->expects($this->once())
            ->method('getString')->with('name')->willReturn('');
        
        $this->expectException(ValidationException::class);
        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $controller->update();         
    }

    public function testIfOrganizationIdNotExistsWithUpdate(): void
    {
        $this->request->expects($this->once())
            ->method('getParams')->willReturn([]);        
        
        $this->expectException(ValidationException::class);
        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $controller->update();         
    }

    public function testIfAccessForbiddenWithUpdate(): void
    {
        $this->request->expects($this->once())
            ->method('getParams')->willReturn(['id' => 3]);
        $org_id = new OrganizationId(3);
        $this->gate->expects($this->once())
            ->method('authorize')
            ->with(OrganizationPolicy::class, 'update', $org_id)
            ->willThrowException(new ForbiddenException('Error 403: access forbidden'));       
        
        $this->request->expects($this->never())
            ->method('getString');
        $this->expectException(ForbiddenException::class);

        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $controller->update();         
    }

    public function testIfDeleteRunCorrect(): void
    {
        $this->request->expects($this->once())
            ->method('getParams')->willReturn(['id' => 3]);
        $org_id = new OrganizationId(3);
        $this->gate->expects($this->once())
            ->method('authorize')->with(OrganizationPolicy::class, 'delete', $org_id);
        $this->orgService->expects($this->once())
            ->method('deleteFromBd')->with($org_id->orgId);
    
        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $result = $controller->delete(); 
        $code = $result->getStatusCode();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(204, $code);
    }

    public function testIfOrganizationIdNotExistsWithDelete(): void
    {
        $this->request->expects($this->once())
            ->method('getParams')->willReturn([]);        
        
        $this->expectException(ValidationException::class);
        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $controller->delete();
    }

    public function testIfAccessForbiddenWithDelete(): void
    {
        $this->request->expects($this->once())
            ->method('getParams')->willReturn(['id' => 3]);
        $org_id = new OrganizationId(3);
        $this->gate->expects($this->once())
            ->method('authorize')
            ->with(OrganizationPolicy::class, 'delete', $org_id)
            ->willThrowException(new ForbiddenException('Error 403: access forbidden'));       
        
        $this->orgService->expects($this->never())
            ->method('deleteFromBd');
        $this->expectException(ForbiddenException::class);

        $controller = new OrganizationController($this->request, 
            $this->orgService, $this->local, $this->gate);   
        $controller->delete();         
    }
}