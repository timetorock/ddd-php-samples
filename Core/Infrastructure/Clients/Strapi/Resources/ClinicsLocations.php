<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Clinic\ClinicLocations;
use App\Models\Interfaces\GetID;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class ClinicsLocations extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $object, $locale = null): object
    {
        $parameters = ['clinic_location_id' => $object->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $clinicsLocations = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_CLINICS_LOCATIONS,
            [],
            build_query_string($parameters)
        ));

        if (empty($clinicsLocations[0])) {
            return new StdClass();
        }

        return $clinicsLocations[0];
    }

    /**
     * @param ClinicLocations $clinicLocation
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(ClinicLocations $clinicLocation)
    {
        $clinicLocationsTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_CLINICS_LOCATIONS,
            [
                'json' => [
                    'clinic_location_id' => $clinicLocation->id,
                    'name'               => $clinicLocation->name,
                ],
            ],
        ));

        if (!is_object($clinicLocationsTranslation) || empty($clinicLocationsTranslation->id)) {
            return new StdClass();
        }

        return $clinicLocationsTranslation;
    }

    /**
     * @param ClinicLocations $clinicLocation
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(ClinicLocations $clinicLocation)
    {
        $clinicLocationsTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_CLINICS_LOCATIONS, $clinicLocation->localization_id),
            [
                'json' => [
                    'name'               => $clinicLocation->name,
                ],
            ],
        ));

        if (!is_object($clinicLocationsTranslation) || empty($clinicLocationsTranslation->id)) {
            return new StdClass();
        }

        return $clinicLocationsTranslation;
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
            self::ENDPOINT_CLINICS_LOCATIONS,
            [],
            build_query_string($params)
        ));
    }
}
