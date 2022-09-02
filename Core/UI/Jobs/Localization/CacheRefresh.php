<?php

namespace App\Jobs\Localization;

use App\Core\Infrastructure\Cache\RedisKeys;
use App\Core\Infrastructure\Services\ApiTranslator\ApiTranslator;
use App\Core\Infrastructure\Services\ApiTranslator\CachedApiTranslator;
use App\Models\Interfaces\GetID;
use Illuminate\Bus\Queueable;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Refresh localization forever cache in separate job to avoid request delay for users.
 * @package App\Jobs\Localization
 */
class CacheRefresh implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var GetID
     */
    private $object;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $locale;

    /**
     * Create a new job instance.
     *
     * @param GetID  $object $object
     * @param string $key
     * @param string $locale
     */
    public function __construct(
        GetID $object,
        string $key,
        string $locale
    ) {
        $this->object = $object;
        $this->key = $key;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     *
     * @param CacheRepository $cacheRepository
     *
     * @param ApiTranslator   $apiTranslator
     *
     * @return void
     * @throws \App\Core\Infrastructure\Services\ApiTranslator\InvalidDataException
     */
    public function handle(
        CacheRepository $cacheRepository,
        ApiTranslator $apiTranslator
    ) {
        $localizationValue = $apiTranslator->get($this->object, $this->key, $this->locale);

        $cacheRepository->forever(
            sprintf(
                '%s:%s:%s:%s:%s',
                RedisKeys::TRANSLATOR_API, get_class($this->object), $this->object->getID(), $this->key, $this->locale
            ),
            $localizationValue
        );

        $cacheRepository->put(
            sprintf(
                '%s:%s:%s:%s:%s',
                RedisKeys::TRANSLATOR_API_WARM, get_class($this->object), $this->object->getID(), $this->key, $this->locale
            ),
            $localizationValue,
            CachedApiTranslator::CACHE_TIME
        );
    }
}
