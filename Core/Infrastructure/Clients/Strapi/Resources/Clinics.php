<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Clinic;
use App\Models\Interfaces\GetID;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class Clinics extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $object, $locale = null): object
    {
        $parameters = ['clinic_id' => $object->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $surgeons = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_CLINICS,
            [],
            build_query_string($parameters)
        ));

        if (empty($surgeons[0])) {
            return new StdClass();
        }

        return $surgeons[0];
    }

    /**
     * @param Clinic $clinic
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(Clinic $clinic)
    {
        $clinicTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_CLINICS,
            [
                'json' => [
                    'clinic_id' => $clinic->id,
                    'name'      => $clinic->name,
                    'about'     => $clinic->about,
                    'alt_tag'   => $clinic->alt_tag,
                ],
            ],
        ));

        if (!is_object($clinicTranslation) || empty($clinicTranslation->id)) {
            return new StdClass();
        }

        return $clinicTranslation;
    }

    /**
     * @param Clinic $clinic
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(Clinic $clinic)
    {
        $clinicTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_CLINICS, $clinic->localization_id),
            [
                'json' => [
                    'name'    => $clinic->name,
                    'about'   => $clinic->about,
                    'alt_tag' => $clinic->alt_tag,
                ],
            ],
        ));

        if (!is_object($clinicTranslation) || empty($clinicTranslation->id)) {
            return new StdClass();
        }

        return $clinicTranslation;
    }

    /**
     * @param array $params
     *
     * @return array|null
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function all(array $params = []): ?array
    {
        return $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_CLINICS,
            [],
            build_query_string($params)
        ));
    }
}
