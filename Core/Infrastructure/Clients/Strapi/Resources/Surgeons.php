<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Interfaces\GetID;
use App\Models\Surgeon;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class Surgeons extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $object, $locale = null): object
    {
        $parameters = ['surgeon_id' => $object->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $surgeons = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_SURGEONS,
            [],
            build_query_string($parameters)
        ));

        if (empty($surgeons[0])) {
            return new StdClass();
        }

        return $surgeons[0];
    }

    /**
     * @param Surgeon $surgeon
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(Surgeon $surgeon)
    {
        $surgeonTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_SURGEONS,
            [
                'json' => [
                    'surgeon_id' => $surgeon->id,
                    'name'       => $surgeon->name,
                    'last_name'  => $surgeon->last_name,
                    'title'      => $surgeon->title,
                    'biography'  => $surgeon->biography,
                    'alt_tag'    => $surgeon->getAltTag(),
                ],
            ],
        ));

        if (!is_object($surgeonTranslation) || empty($surgeonTranslation->id)) {
            return new StdClass();
        }

        return $surgeonTranslation;
    }

    /**
     * @param Surgeon $surgeon
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(Surgeon $surgeon)
    {
        $surgeonTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_SURGEONS, $surgeon->localization_id),
            [
                'json' => [
                    'name'      => $surgeon->name,
                    'last_name' => $surgeon->last_name,
                    'title'     => $surgeon->title,
                    'biography' => $surgeon->biography,
                    'alt_tag'   => $surgeon->getAltTag(),
                ],
            ],
        ));

        if (!is_object($surgeonTranslation) || empty($surgeonTranslation->id)) {
            return new StdClass();
        }

        return $surgeonTranslation;
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
            self::ENDPOINT_SURGEONS,
            [],
            build_query_string($params)
        ));
    }
}
