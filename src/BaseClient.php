<?php


namespace Monaz\VirusTotal;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

use Monaz\VirusTotal\Exceptions\{
    AlreadyExistsException,
    AuthenticationRequiredException,
    BadRequestException,
    DeadlineExceededException,
    FailedDependencyException,
    ForbiddenException,
    InvalidArgumentException,
    MalformedResponseException,
    NotAvailableYetException,
    QuotaExceededException,
    TooManyRequestsException,
    TransientException,
    UnselectiveContentQueryException,
    UnsupportedContentQueryException,
    UserNotActiveException,
    WrongCredentialsException
};

/**
 * Light-weight Factory to construct HTTP calls
 */
class BaseClient
{
    /**
     * @var string - Virus Total API endpoint prefix
     */
    const API_ENDPOINT = 'https://www.virustotal.com/api/v3/';

    /**
     * @var ClientInterface - http client
     */
    protected $_client;

    /**
     * @var string - virus total api key
     */
    protected $_apiKey;

    /**
     * Constructor
     * @param string $apiKey
     * @param ClientInterface|null $client
     */
    public function __construct(string $apiKey, ClientInterface $client = null)
    {
        $this->_apiKey =  $apiKey;

        if ($client) {
            $this->_client = $client;
        } else {
            $this->_client = new Client([
                'base_uri' => self::API_ENDPOINT,
                'headers'  => [
                    'Accept' => 'application/json',
                    'x-apikey' => $this->_apiKey,
                ]
            ]);
        }
    }

    /**
     * Util function to make requests.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    public function makeRequest(string $method, string $endpoint, array $params = []): array
    {
        try {
            $response = $this->_client->$method($endpoint, $params);
        } catch(GuzzleException $e) {
            if(!$e instanceof ClientException){
                throw $e;
            }
            $response = $e->getResponse();
        }

        return $this->validateResponse($response);
    }

    /**
     * Util function to make post request
     *
     * @param string $endpoint
     * @param array|null $params
     * @return array
     */
    public function makePostRequest(string $endpoint, array $params = [], string $type = 'form_params'): array
    {
        $payload = [
            $type => $params
        ];

        return $this->makeRequest("post", $endpoint, $payload);
    }


    /**
     * Util function to make get request
     *
     * @param string $endpoint
     * @param array|null $params
     * @return array
     */
    public function makeGetRequest(string $endpoint, array $params = []): array
    {
        $url = self::API_ENDPOINT . $endpoint;
        if($params) {
            $url .= '?'. http_build_query($params);
        }

        return $this->makeRequest("get", $url);
    }


    /**
     * Util function to make patch request
     *
     * @param string $endpoint
     * @param array|null $params
     * @return array
     */
    public function makePatchRequest(string $endpoint, array $params = []): array
    {
        return $this->makeRequest("patch", $endpoint, $params);
    }

    /**
     * Util function to make delete request
     *
     * @param string $endpoint
     * @return array
     */
    public function makeDeleteRequest(string $endpoint): array
    {
        return $this->makeRequest("delete", $endpoint);
    }

    /**
     * Convert Guzzle response to array.
     *
     * @param ResponseInterface $response
     * @return array
     */
    protected function to_json(ResponseInterface $response): array
    {
        return json_decode($response->getBody(), true);
    }

    /**
     * Extract data key from json response.
     *
     * @param array $response
     * @return array
     * @throws MalformedResponseException
     */
    protected function extract_data_from_json(array $response): array
    {
        if(!isset($response["data"])) {
            throw new MalformedResponseException("No Data inside response.");
        }

        return $response["data"];
    }

    /**
     * Validate response by looking up the http response code.
     * This will dynamically create and raise exceptions.
     *
     * @param ResponseInterface $response
     * @throws MalformedResponseException
     * @throws NotAvailableYetException
     * @throws AlreadyExistsException
     * @throws AuthenticationRequiredException
     * @throws BadRequestException
     * @throws DeadlineExceededException
     * @throws FailedDependencyException
     * @throws ForbiddenException
     * @throws InvalidArgumentException
     * @throws MalformedResponseException
     * @throws NotAvailableYetException
     * @throws QuotaExceededException
     * @throws TooManyRequestsException
     * @throws TransientException
     * @throws UnselectiveContentQueryException
     * @throws UnsupportedContentQueryException
     * @throws UserNotActiveException
     * @throws WrongCredentialsException
     */
    protected function validateResponse(ResponseInterface $response)
    {
        $body = $this->to_json($response);

        if($response->getStatusCode() < 400) {
            return $this->extract_data_from_json($body);
        }

        if(!isset($body["error"]) || !isset($body["error"]["code"])) {
            throw new MalformedResponseException();
        }

        $error = $body["error"];

        if($error["code"] === "NotAvailableYet") {
            throw new NotAvailableYetException($error["message"]);
        }

        $exceptionClass = '\\Exceptions\\'.str_replace("Error", "Exception", $error["code"]);

        if(file_exists(__dir__.$exceptionClass.'.php')) {
            $exceptionClassWithNamespace = __NAMESPACE__ . $exceptionClass;
            throw new $exceptionClassWithNamespace($error["message"]);
        }

        throw new MalformedResponseException();
    }
}
