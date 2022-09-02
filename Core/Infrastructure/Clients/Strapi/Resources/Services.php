<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Interfaces\GetID;
use App\Models\Service;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class Services extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $service, $locale = null): object
    {
        $parameters = ['service_id' => $service->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $services = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_SERVICES,
            [],
            build_query_string($parameters)
        ));

        if (empty($services[0])) {
            return new StdClass();
        }

        return $services[0];
    }

    /**
     * @param Service $service
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(Service $service)
    {
        $serviceTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_SERVICES,
            [
                'json' => [
                    'service_id' => $service->id,
                    'name'       => $service->name,
                ],
            ],
        ));

        if (!is_object($serviceTranslation) || empty($serviceTranslation->id)) {
            return new StdClass();
        }

        return $serviceTranslation;
    }

    /**
     * @param Service $service
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(Service $service)
    {
        $serviceTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_SERVICES, $service->localization_id),
            [
                'json' => [
                    'name' => $service->name,
                ],
            ],
        ));

        if (!is_object($serviceTranslation) || empty($serviceTranslation->id)) {
            return new StdClass();
        }

        return $serviceTranslation;
    }

    /**
     * @param array $params
     *
     * @return array|null
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function all(array $params = [])
    {
        return $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_SERVICES,
            [],
            build_query_string($params)
        ));
    }
}
