<?php

namespace Tests\Monaz\VirusTotal;

use GuzzleHttp\Client;
use Monaz\VirusTotal\BaseClient;
use Monaz\VirusTotal\Domain;
use PHPUnit\Framework\TestCase;
use Tests\Monaz\VirusTotal\ResponseMocks\DomainResponse;
use Tests\Monaz\VirusTotal\Utils\ArrayAssertionTrait;

class DomainTest extends TestCase
{
    use ArrayAssertionTrait;

    private function mockClientWithMakeRequest()
    {
        return $this->getMockBuilder(Domain::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['makeGetRequest'])
            ->getMock();
    }

    public function testConstructor_CreatedInstanceIsOfTheBaseClientType()
    {
        $domainStub = new Domain(GLOBAL_API_KEY, new Client());
        $this->assertInstanceOf(BaseClient::class, $domainStub);
    }

    public function testGetReport_WhenCalledWithProperData_ResponseHasReputationAndStats()
    {
        $ipStub = $this->mockClientWithMakeRequest();
        $ipStub->expects($this->once())
            ->method('makeGetRequest')
            ->with('domains/mohsen.codes')
            ->willReturn(DomainResponse::get());


        $response = $ipStub->getReport("mohsen.codes");
        $this->assertArrayStructure(["attributes" => ["reputation", "last_analysis_stats"]], $response);
    }
}
