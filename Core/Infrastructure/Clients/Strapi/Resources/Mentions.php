<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Interfaces\GetID;
use App\Models\Mention;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class Mentions extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $mention, $locale = null): object
    {
        $parameters = ['mention_id' => $mention->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $mentions = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_MENTIONS,
            [],
            build_query_string($parameters)
        ));

        if (empty($mentions[0])) {
            return new StdClass();
        }

        return $mentions[0];
    }

    /**
     * @param Mention $mention
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(Mention $mention)
    {
        $mentionTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_MENTIONS,
            [
                'json' => [
                    'mention_id' => $mention->id,
                    'title'      => $mention->title,
                ],
            ],
        ));

        if (!is_object($mentionTranslation) || empty($mentionTranslation->id)) {
            return new StdClass();
        }

        return $mentionTranslation;
    }

    /**
     * @param Mention $mention
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(Mention $mention)
    {
        $mentionTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_MENTIONS, $mention->localization_id),
            [
                'json' => [
                    'title' => $mention->title,
                ],
            ],
        ));

        if (!is_object($mentionTranslation) || empty($mentionTranslation->id)) {
            return new StdClass();
        }

        return $mentionTranslation;
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
            self::ENDPOINT_MENTIONS,
            [],
            build_query_string($params)
        ));
    }
}
