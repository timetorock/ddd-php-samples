<?php

namespace App\Core\Infrastructure\Clients\Strapi\Resources;

use App\Core\Infrastructure\Clients\Strapi\Http\Client;
use Psr\Http\Message\ResponseInterface;

abstract class Resource implements StrAPIEntity
{

    const METHOD_GET  = 'get';
    const METHOD_POST = 'post';

    const ENDPOINT_AUTH = '/auth/local';

    /**
     * Surgeons
     */
    const ENDPOINT_CLINICS             = '/clinics';
    const ENDPOINT_CLINICS_LOCATIONS   = '/clinics-locations';
    const ENDPOINT_SURGEONS            = '/surgeons';
    const ENDPOINT_SURGEONS_ACTIVITIES = '/surgeons-activities';
    const ENDPOINT_REVIEWS             = '/reviews';
    const ENDPOINT_PACKAGES            = '/packages';
    const ENDPOINT_SERVICES            = '/services';
    const ENDPOINT_MENTIONS            = '/mentions';
    const ENDPOINT_BEFORE_AFTER        = '/before-afters';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array|null
     */
    protected function getDataFromResponse(ResponseInterface $response)
    {
        $contents = $response->getBody()->getContents();

        return $contents ? json_decode($contents) : null;
    }
}
