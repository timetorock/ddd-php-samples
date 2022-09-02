<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Interfaces\GetID;
use App\Models\SurgeonActivities;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class SurgeonsActivities extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $object, $locale = null): object
    {
        $parameters = ['surgeons_activity_id' => $object->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $surgeonActivities = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_SURGEONS_ACTIVITIES,
            [],
            build_query_string($parameters)
        ));

        if (empty($surgeonActivities[0])) {
            return new StdClass();
        }

        return $surgeonActivities[0];
    }

    /**
     * @param SurgeonActivities $surgeonActivities
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(SurgeonActivities $surgeonActivities)
    {
        $surgeonActivitiesTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_SURGEONS_ACTIVITIES,
            [
                'json' => [
                    'surgeons_activity_id' => $surgeonActivities->id,
                    'value'                => $surgeonActivities->value,
                ],
            ],
        ));

        if (!is_object($surgeonActivitiesTranslation) || empty($surgeonActivitiesTranslation->id)) {
            return new StdClass();
        }

        return $surgeonActivitiesTranslation;
    }

    /**
     * @param SurgeonActivities $surgeonActivities
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(SurgeonActivities $surgeonActivities)
    {
        $surgeonActivitiesTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_SURGEONS_ACTIVITIES, $surgeonActivities->localization_id),
            [
                'json' => [
                    'value' => $surgeonActivities->value,
                ],
            ],
        ));

        if (!is_object($surgeonActivitiesTranslation) || empty($surgeonActivitiesTranslation->id)) {
            return new StdClass();
        }

        return $surgeonActivitiesTranslation;
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
            self::ENDPOINT_SURGEONS_ACTIVITIES,
            [],
            build_query_string($params)
        ));
    }
}
