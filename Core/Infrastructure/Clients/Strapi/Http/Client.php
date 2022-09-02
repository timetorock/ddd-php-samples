<?php


namespace App\Core\Infrastructure\Clients\Strapi\Http;

use App\Core\Infrastructure\Cache\RedisKeys;
use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Core\Infrastructure\Clients\Strapi\Exceptions\UnknownErrorException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Client
{
    private const CACHE_TIME = 60 * 60 * 24 * 15; // 15 days

    /**
     * @var string
     */
    protected $jwt;

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * Guzzle allows options into its request method. Prepare for some defaults.
     *
     * @var array
     */
    protected $clientOptions = [];

    /**
     * @var string
     */
    protected $user_agent = 'TrustSurgeons.com (backend)';

    /**
     * Client constructor.
     *
     * @param string $endpoint
     * @param string $identifier
     * @param string $password
     *
     * @throws UnknownErrorException
     */
    public function __construct(
        string $endpoint,
        string $identifier,
        string $password
    ) {
        $this->client = new GuzzleClient([
            'base_uri' => $endpoint,
        ]);

        try {
            $this->jwt = Cache::remember(RedisKeys::STRAPI_AUTH_JWT, self::CACHE_TIME, function () use ($identifier, $password) {
                return $this->jwtUserAuth($identifier, $password);
            });
        } catch (Throwable $e) {
            $previous = $e->getPrevious();
            /**
             * Temporary hack to log error, as we need to have stable API key on StrAPI
             * Stable API ket will make library work valid, and we will not need any Logs here.
             */
            Log::error("StrAPI JWT authorization failed", [
                'message' => $e->getMessage(),
                'previous'=> $previous ? $previous->getMessage() : '',
            ]);
        }

    }

    /**
     * @param string $method       The HTTP request verb
     * @param string $endpoint     The Hubspot API endpoint
     * @param array  $options      An array of options to send with the request
     * @param null   $query_string A query string to send with the request
     *
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public function request(string $method, string $endpoint, array $options = [], $query_string = null)
    {
        if (empty($this->jwt)) {
            throw new InvalidArgumentException('You must be authorized via Auth JWT first.');
        }

        $url = $this->generateUrl($endpoint, $query_string);

        $options = array_merge($this->clientOptions, $options);
        $options['headers']['User-Agent'] = $this->user_agent;

        if ($this->jwt) {
            $options['headers']['Authorization'] = 'Bearer ' . $this->jwt;
        }

        return $this->client->request($method, $url, $options);
    }

    /**
     * Generate the full endpoint url, including query string.
     *
     * @param string $endpoint     the API endpoint
     * @param null   $query_string the query string to send to the endpoint
     *
     * @return string
     */
    protected function generateUrl(string $endpoint, $query_string = null)
    {
        if (empty($query_string)) {
            return $endpoint;
        }

        return $endpoint . '?' . $query_string;
    }

    /**
     * @param string $identifier
     * @param string $password
     *
     * @return string
     * @throws UnknownErrorException
     */
    protected function jwtUserAuth(string $identifier, string $password)
    {
        try {
            $response = $this->client->request(
                'post',
                'auth/local',
                ['json' =>
                     [
                         'identifier' => $identifier,
                         'password'   => $password,
                     ],
                ],
            );
        } catch (GuzzleException $e) {
            throw new UnknownErrorException('User authorization request failed', 0, $e);
        }

        $contents = $response->getBody()->getContents();

        $authResponse = $contents ? json_decode($contents) : null;
        if (empty($authResponse->jwt)) {
            throw new UnknownErrorException('User authorization failed, empty jwt');
        }

        return $authResponse->jwt;
    }

}
