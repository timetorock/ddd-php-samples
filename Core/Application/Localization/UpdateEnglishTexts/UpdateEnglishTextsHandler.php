<?php


namespace App\Core\Application\Localization\UpdateEnglishTexts;


use App\Core\Application\Localization\GenerateDynamicEnglishTranslations\TranslatorEntityProcessorNotExists;
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
use App\Models\Mention;
use App\Models\Package;
use App\Models\Review;
use App\Models\Service;
use App\Models\Surgeon;
use App\Models\SurgeonActivities;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateEnglishTextsHandler
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
        $this->entities[ SurgeonActivities::class ] = new SurgeonsActivities($client);
        $this->entities[ Review::class ] = new Reviews($client);
        $this->entities[ BeforeAfterResult::class ] = new BeforeAfter($client);
        $this->entities[ Package::class ] = new Packages($client);
        $this->entities[ Service::class ] = new Services($client);
        $this->entities[ Mention::class ] = new Mentions($client);
    }

    public function handle()
    {
        Clinic::where('localization_required', '=', 1)->chunk(100, function ($clinics) {
            foreach ($clinics as $clinic) {
                $this->processEntityType($clinic);
            }
        });

        Clinic\ClinicLocations::where('localization_required', '=', 1)->chunk(100, function ($clinicLocations) {
            foreach ($clinicLocations as $clinicLocation) {
                $this->processEntityType($clinicLocation);
            }
        });

        Surgeon::where('localization_required', '=', 1)->chunk(100, function ($surgeons) {
            foreach ($surgeons as $surgeon) {
                $this->processEntityType($surgeon);
            }
        });

        SurgeonActivities::where('localization_required', '=', 1)->chunk(100, function ($surgeonActivities) {
            foreach ($surgeonActivities as $surgeonActivity) {
                $this->processEntityType($surgeonActivity);
            }
        });

        Review::where('localization_required', '=', 1)->chunk(100, function ($reviews) {
            foreach ($reviews as $review) {
                $this->processEntityType($review);
            }
        });

        BeforeAfterResult::where('localization_required', '=', 1)->chunk(100, function ($beforeAfterResults) {
            foreach ($beforeAfterResults as $beforeAfterResult) {
                $this->processEntityType($beforeAfterResult);
            }
        });

        Package::where('localization_required', '=', 1)->chunk(100, function ($packages) {
            foreach ($packages as $package) {
                $this->processEntityType($package);
            }
        });

        Service::where('localization_required', '=', 1)->chunk(100, function ($services) {
            foreach ($services as $service) {
                $this->processEntityType($service);
            }
        });

        Mention::where('localization_required', '=', 1)->chunk(100, function ($services) {
            foreach ($services as $service) {
                $this->processEntityType($service);
            }
        });
    }

    /**
     * @param $entity
     *
     * @throws TranslatorEntityProcessorNotExists
     */
    private function processEntityType($entity)
    {
        $class = get_class($entity);

        if (empty($this->entities[ $class ])) {
            throw new TranslatorEntityProcessorNotExists();
        }

        /**
         * @var $entityProcessor StrAPIEntity
         */
        $entityProcessor = $this->entities[ $class ];

        try {
            $entityProcessor->update($entity);
        } catch (Throwable $e) {
            Log::error("Can't update entity ID on translator", [
                'exception'   => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'entity_id'   => $entity->id,
                'entity_type' => $class,
            ]);

            return;
        }

        $entity->localization_required = 0;
        $entity->save();
    }
}
