<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Interfaces\GetID;
use App\Models\Package;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class Packages extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $object, $locale = null): object
    {
        $parameters = ['package_id' => $object->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $packages = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_PACKAGES,
            [],
            build_query_string($parameters)
        ));

        if (empty($packages[0])) {
            return new StdClass();
        }

        return $packages[0];
    }

    /**
     * @param Package $package
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(Package $package)
    {
        $packageTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_PACKAGES,
            [
                'json' => [
                    'package_id' => $package->id,
                    'name'       => $package->name,
                ],
            ],
        ));

        if (!is_object($packageTranslation) || empty($packageTranslation->id)) {
            return new StdClass();
        }

        return $packageTranslation;
    }

    /**
     * @param Package $package
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(Package $package)
    {
        $packageTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_PACKAGES, $package->localization_id),
            [
                'json' => [
                    'name' => $package->name,
                ],
            ],
        ));

        if (!is_object($packageTranslation) || empty($packageTranslation->id)) {
            return new StdClass();
        }

        return $packageTranslation;
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
            self::ENDPOINT_PACKAGES,
            [],
            build_query_string($params)
        ));
    }
}
