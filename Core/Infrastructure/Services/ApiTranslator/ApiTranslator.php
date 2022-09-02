<?php


namespace App\Core\Infrastructure\Services\ApiTranslator;


use App\Core\Infrastructure\Clients\Strapi\Http\Client;
use App\Core\Infrastructure\Clients\Strapi\Resources\BeforeAfter;
use App\Core\Infrastructure\Clients\Strapi\Resources\Clinics;
use App\Core\Infrastructure\Clients\Strapi\Resources\ClinicsLocations;
use App\Core\Infrastructure\Clients\Strapi\Resources\Mentions;
use App\Core\Infrastructure\Clients\Strapi\Resources\Packages;
use App\Core\Infrastructure\Clients\Strapi\Resources\Reviews;
use App\Core\Infrastructure\Clients\Strapi\Resources\Services;
use App\Core\Infrastructure\Clients\Strapi\Resources\StrAPIEntity;
use App\Core\Infrastructure\Clients\Strapi\Resources\Surgeons;
use App\Core\Infrastructure\Clients\Strapi\Resources\SurgeonsActivities;
use App\Models\BeforeAfterResult;
use App\Models\Clinic;
use App\Models\Interfaces\GetID;
use App\Models\Mention;
use App\Models\Package;
use App\Models\Review;
use App\Models\Service;
use App\Models\Surgeon;
use App\Models\SurgeonActivities;
use Log;
use Throwable;

class ApiTranslator implements TranslatorInterface
{
    /**
     * @var array
     */
    protected $entities = [];

    /**
     * ApiTranslator constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->entities[ Clinic::class ] = new Clinics($client);
        $this->entities[ Clinic\ClinicLocations::class ] = new ClinicsLocations($client);
        $this->entities[ Surgeon::class ] = new Surgeons($client);
        $this->entities[ Review::class ] = new Reviews($client);
        $this->entities[ SurgeonActivities::class ] = new SurgeonsActivities($client);
        $this->entities[ BeforeAfterResult::class ] = new BeforeAfter($client);
        $this->entities[ Package::class ] = new Packages($client);
        $this->entities[ Service::class ] = new Services($client);
        $this->entities[ Mention::class ] = new Mentions($client);
    }

    /**
     * @param GetID  $object
     * @param string $key
     * @param null   $locale
     *
     * @return string
     * @throws InvalidDataException
     */
    public function get(GetID $object, string $key, $locale = null): ?string
    {
        if (empty($object)) {
            throw new InvalidDataException('no translatable object');
        }

        if (empty($key)) {
            throw new InvalidDataException('no translatable key');
        }

        if ($locale === "en") {
            return $object->$key;
        }

        $class = get_class($object);

        if (empty($this->entities[ $class ])) {
            return $object->$key;
        }

        /**
         * @var StrAPIEntity $apiClass
         */
        $apiClass = $this->entities[ $class ];

        try {
            $apiObject = $apiClass->entityID($object, $locale);

            if (!empty($apiObject->$key)) {
                return $apiObject->$key;
            }
        } catch (Throwable $e) {
            Log::error("can't translate entity object, api error", [
                'exception'   => $e->getMessage(),
                'objectID'    => $object->id,
                'objectClass' => $class,
            ]);
        }

        return $object->$key;
    }


}
