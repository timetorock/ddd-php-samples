<?php


namespace App\Core\Infrastructure\Clients\Strapi\Resources;


use App\Core\Infrastructure\Clients\Strapi\Exceptions\InvalidArgumentException;
use App\Models\Interfaces\GetID;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

interface StrAPIEntity
{
    /**
     * Request specific surgeon content by it's ID
     *
     * @param GetID $object
     * @param null  $locale
     *
     * @return mixed|stdClass
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public function entityID(GetID $object, $locale = null): object;

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function all(array $params = []);
}
