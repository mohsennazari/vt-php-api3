<?php


namespace Tests\Monaz\VirusTotal;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Monaz\VirusTotal\BaseClient;
use Monaz\VirusTotal\Exceptions\BadRequestException;
use Monaz\VirusTotal\Exceptions\MalformedResponseException;
use Monaz\VirusTotal\Exceptions\NotAvailableYetException;
use PHPUnit\Framework\TestCase;

class BaseClientTest extends TestCase
{
    private function mockClientResponse($status, $response, $headers = [])
    {
        $mock = new MockHandler([
            new Response(
                $status,
                $headers,
                json_encode($response)
            )
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        return new BaseClient(GLOBAL_API_KEY, $client);
    }

    private function mockBaseClientWithMakeRequest()
    {
        return $this->getMockBuilder(BaseClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['makeRequest'])
            ->getMock();
    }

    public function testConstructor_CreatedInstanceIsOfTheBaseClientType()
    {
        $baseClientStub = new BaseClient(GLOBAL_API_KEY, new Client());
        $this->assertInstanceOf(BaseClient::class, $baseClientStub);
    }

    public function testMakeRequest_WhenReceivesProperResponse_ReturnsArray()
    {
        $baseClientStub = $this->mockClientResponse(200, [
            "data" => ["something"]
        ]);

        $response = $baseClientStub->makeRequest("post", 'test', []);

        $this->assertIsArray($response);
    }

    public function testMakeRequest_WhenExceptionOtherThanClientExceptionOccurs_ThrowsTheException()
    {
        $guzzleStub = $this->createMock(Client::class);

        $guzzleStub->expects($this->any())
            ->method('post')
            ->willThrowException(new \Exception("Some test issue."));

        $baseClientStub = new BaseClient(GLOBAL_API_KEY, $guzzleStub);


        $this->expectException(\Exception::class);
        $baseClientStub->makeRequest("post", 'test', []);
    }

    public function testMakeRequest_WhenReceivesResponseWithoutDataAttribute_ThrowsMalformedException()
    {
        $baseClientStub = $this->mockClientResponse(200, [
            "not-data" => ["something"]
        ]);

        $this->expectException(MalformedResponseException::class);
        $baseClientStub->makeRequest("post", 'test', []);
    }

    public function testMakeRequest_WhenReceivesErrorResponseWithErrorAttribute_ThrowsProperException()
    {
        $baseClientStub = $this->mockClientResponse(400, [
            "error" => [
                "code" => "BadRequestError",
                "message" => "Stub Message"
            ]
        ]);

        $this->expectException(BadRequestException::class);
        $baseClientStub->makeRequest("post", 'test', []);
    }

    public function testMakeRequest_WhenReceivesErrorResponseWithErrorAttribute_ThrownExceptionHasMessage()
    {
        $baseClientStub = $this->mockClientResponse(400, [
            "error" => [
                "code" => "BadRequestError",
                "message" => "Stub Message"
            ]
        ]);

        $this->expectExceptionMessage("Stub Message");
        $baseClientStub->makeRequest("post", 'test', []);
    }

    public function testMakeRequest_WhenReceivesNotAvailableYetErrorResponse_ThrowsNotAvailableYetException()
    {
        $baseClientStub = $this->mockClientResponse(400, [
            "error" => [
                "code" => "NotAvailableYet",
                "message" => "Stub Message"
            ]
        ]);

        $this->expectException(NotAvailableYetException::class);
        $baseClientStub->makeRequest("post", 'test', []);
    }

    public function testMakeRequest_WhenReceivesErrorResponseWithoutErrorAttribute_ThrowsMalformedException()
    {
        $baseClientStub = $this->mockClientResponse(400, [
            "not-error" => ["something"]
        ]);

        $this->expectException(MalformedResponseException::class);
        $baseClientStub->makeRequest("post", 'test', []);
    }

    public function testMakeRequest_WhenReceivesErrorResponseWithErrorWithoutCodeAttribute_ThrowsMalformedException()
    {
        $baseClientStub = $this->mockClientResponse(400, [
            "error" => ["something"]
        ]);

        $this->expectException(MalformedResponseException::class);
        $baseClientStub->makeRequest("post", 'test', []);
    }

    public function testMakeRequest_WhenReceivesErrorResponseWithErrorCodeNotHandled_ThrowsMalformedException()
    {
        $baseClientStub = $this->mockClientResponse(400, [
            "error" => [
                "code" => "SomeNewErrorWeHaveNotConsidered",
                "message" => "Stub Message"
            ]
        ]);

        $this->expectException(MalformedResponseException::class);
        $baseClientStub->makeRequest("post", 'test', []);
    }

    public function testMakePostRequest_WhenMakeRequestIsCalled_ThePayloadIsBuiltWithType()
    {
        $baseClientStub = $this->mockBaseClientWithMakeRequest();
        $baseClientStub->expects($this->once())
            ->method('makeRequest')
            ->with('post', 'test-post', [
                "form_params" => [
                    "key" => "value"
                ]
            ]);

        $baseClientStub->makePostRequest("test-post", ["key" => "value"]);
    }

    public function testMakeGetRequest_WhenMakeRequestIsCalled_ThePayloadIsBuiltWithQuery()
    {
        $baseClientStub = $this->mockBaseClientWithMakeRequest();
        $baseClientStub->expects($this->once())
            ->method('makeRequest')
            ->with('get', BaseClient::API_ENDPOINT.'test-get?key=value&key2=value2');

        $baseClientStub->makeGetRequest("test-get", ["key" => "value", "key2" => "value2"]);
    }

    public function testMakePatchRequest_WhenMakeRequestIsCalled_ItIsCalled()
    {
        $baseClientStub = $this->mockBaseClientWithMakeRequest();
        $baseClientStub->expects($this->once())
            ->method('makeRequest');

        $baseClientStub->makePatchRequest("test-patch");
    }

    public function testMakeDeleteRequest_WhenMakeRequestIsCalled_ItIsCalled()
    {
        $baseClientStub = $this->mockBaseClientWithMakeRequest();
        $baseClientStub->expects($this->once())
            ->method('makeRequest');

        $baseClientStub->makeDeleteRequest("test-delete");
    }
}
