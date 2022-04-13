<?php

namespace Tests\Monaz\VirusTotal;

use GuzzleHttp\Client;
use Monaz\VirusTotal\BaseClient;
use Monaz\VirusTotal\Url;
use PHPUnit\Framework\TestCase;
use Tests\Monaz\VirusTotal\ResponseMocks\DomainResponse;
use Tests\Monaz\VirusTotal\Utils\ArrayAssertionTrait;

class UrlTest extends TestCase
{
    use ArrayAssertionTrait;

    private function mockClientWithMakeRequest()
    {
        return $this->getMockBuilder(Url::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['makeGetRequest', 'makePostRequest'])
            ->getMock();
    }

    public function testConstructor_CreatedInstanceIsOfTheBaseClientType()
    {
        $urlStub = new Url(GLOBAL_API_KEY, new Client());
        $this->assertInstanceOf(BaseClient::class, $urlStub);
    }

    public function testGetReport_WhenCalledWithProperData_ResponseHasReputationAndStats()
    {
        $urlStub = $this->mockClientWithMakeRequest();
        $urlStub->expects($this->once())
            ->method('makeGetRequest')
            ->with('urls/test-random-identifier')
            ->willReturn(DomainResponse::get());


        $response = $urlStub->getReport("test-random-identifier");
        $this->assertArrayStructure(["attributes" => ["reputation", "last_analysis_stats"]], $response);
    }
}
