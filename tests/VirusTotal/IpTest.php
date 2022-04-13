<?php

namespace Tests\Monaz\VirusTotal;

use GuzzleHttp\Client;
use Monaz\VirusTotal\BaseClient;
use Monaz\VirusTotal\Ip;
use PHPUnit\Framework\TestCase;
use Tests\Monaz\VirusTotal\ResponseMocks\IpResponse;
use Tests\Monaz\VirusTotal\Utils\ArrayAssertionTrait;

class IpTest extends TestCase
{
    use ArrayAssertionTrait;

    private function mockClientWithMakeRequest()
    {
        return $this->getMockBuilder(Ip::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['makeGetRequest'])
            ->getMock();
    }

    public function testConstructor_CreatedInstanceIsOfTheBaseClientType()
    {
        $ipStub = new Ip(GLOBAL_API_KEY, new Client());
        $this->assertInstanceOf(BaseClient::class, $ipStub);
    }

    public function testGetReport_WhenCalledWithProperData_ResponseHasReputationAndStats()
    {
        $ipStub = $this->mockClientWithMakeRequest();
        $ipStub->expects($this->once())
            ->method('makeGetRequest')
            ->with('ip_addresses/1.1.1.1')
            ->willReturn(IpResponse::get());


        $response = $ipStub->getReport("1.1.1.1");
        $this->assertArrayStructure(["attributes" => ["reputation", "last_analysis_stats"]], $response);
    }
}
