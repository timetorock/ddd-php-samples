<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Interfaces\GetID;
use App\Models\Review;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class Reviews extends Resource
{
    /**
     * @inheritDoc
     */
    public function entityID(GetID $object, $locale = null): object
    {
        $parameters = ['review_id' => $object->getID()];

        if (!empty($locale)) {
            $parameters['_locale'] = $locale;
        }

        $reviews = $this->getDataFromResponse($this->client->request(
            'get',
            self::ENDPOINT_REVIEWS,
            [],
            build_query_string($parameters)
        ));

        if (empty($reviews[0])) {
            return new StdClass();
        }

        return $reviews[0];
    }

    /**
     * @param Review $review
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function create(Review $review)
    {
        $reviewTranslation = $this->getDataFromResponse($this->client->request(
            'post',
            self::ENDPOINT_REVIEWS,
            [
                'json' => [
                    'review_id'     => $review->id,
                    'review'        => $review->review,
                    'reviewer_name' => $review->reviewer_name,
                ],
            ],
        ));

        if (!is_object($reviewTranslation) || empty($reviewTranslation->id)) {
            return new StdClass();
        }

        return $reviewTranslation;
    }

    /**
     * @param Review $review
     *
     * @return mixed|stdClass
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function update(Review $review)
    {
        $reviewTranslation = $this->getDataFromResponse($this->client->request(
            'put',
            sprintf('%s/%s', self::ENDPOINT_REVIEWS, $review->localization_id),
            [
                'json' => [
                    'review'        => $review->review,
                    'reviewer_name' => $review->reviewer_name,
                ],
            ],
        ));

        if (!is_object($reviewTranslation) || empty($reviewTranslation->id)) {
            return new StdClass();
        }

        return $reviewTranslation;
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
            self::ENDPOINT_REVIEWS,
            [],
            build_query_string($params)
        ));
    }
}
