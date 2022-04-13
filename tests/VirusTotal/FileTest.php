<?php

namespace Tests\Monaz\VirusTotal;


use GuzzleHttp\Client;
use Monaz\VirusTotal\BaseClient;
use Monaz\VirusTotal\File;
use PHPUnit\Framework\TestCase;
use Tests\Monaz\VirusTotal\Utils\ArrayAssertionTrait;

class FileTest extends TestCase
{
    use ArrayAssertionTrait;

    private function mockClientWithMakeRequest()
    {
        return $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['makeGetRequest', 'makePostRequest'])
            ->getMock();
    }

    public function testConstructor_CreatedInstanceIsOfTheBaseClientType()
    {
        $fileStub = new File(GLOBAL_API_KEY, new Client());
        $this->assertInstanceOf(BaseClient::class, $fileStub);
    }
}
