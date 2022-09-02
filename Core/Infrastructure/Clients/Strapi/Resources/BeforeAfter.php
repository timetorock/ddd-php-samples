<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\BeforeAfterResult;
use App\Models\Interfaces\GetID;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class BeforeAfter extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $object, $locale = null): object
    {
        $parameters = ['before_after_id' => $object->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $beforeAfterResult = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_BEFORE_AFTER,
            [],
            build_query_string($parameters)
        ));

        if (empty($beforeAfterResult[0])) {
            return new StdClass();
        }

        return $beforeAfterResult[0];
    }

    /**
     * @param BeforeAfterResult $beforeAfter
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(BeforeAfterResult $beforeAfter)
    {
        $beforeAfterTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_BEFORE_AFTER,
            [
                'json' => [
                    'before_after_id' => $beforeAfter->id,
                    'name'            => $beforeAfter->name,
                    'complexity'      => $beforeAfter->complexity,
                    'alt_tag'         => $beforeAfter->alt_tag,
                ],
            ],
        ));

        if (!is_object($beforeAfterTranslation) || empty($beforeAfterTranslation->id)) {
            return new StdClass();
        }

        return $beforeAfterTranslation;
    }

    /**
     * @param BeforeAfterResult $beforeAfter
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(BeforeAfterResult $beforeAfter)
    {
        $beforeAfterTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_BEFORE_AFTER, $beforeAfter->localization_id),
            [
                'json' => [
                    'name'       => $beforeAfter->name,
                    'complexity' => $beforeAfter->complexity,
                    'alt_tag'    => $beforeAfter->alt_tag,
                ],
            ],
        ));

        if (!is_object($beforeAfterTranslation) || empty($beforeAfterTranslation->id)) {
            return new StdClass();
        }

        return $beforeAfterTranslation;
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
            self::ENDPOINT_BEFORE_AFTER,
            [],
            build_query_string($params)
        ));
    }
}
