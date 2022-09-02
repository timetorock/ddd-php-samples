<?php


namespace App\Core\Infrastructure\Services\ApiTranslator;

use App\Core\Infrastructure\Cache\RedisKeys;
use App\Jobs\Localization\CacheRefresh;
use App\Models\Interfaces\GetID;
use Illuminate\Cache\Repository as CacheRepository;

class CachedApiTranslator implements TranslatorInterface
{
    const CACHE_TIME = 60 * 60; // 1 hour

    /**
     * @var ApiTranslator
     */
    private $apiTranslator;

    /**
     * @var CacheRepository
     */
    private $cacheRepository;

    /**
     * CachedApiTranslator constructor.
     *
     * @param ApiTranslator   $apiTranslator
     * @param CacheRepository $cacheRepository
     */
    public function __construct(
        ApiTranslator $apiTranslator,
        CacheRepository $cacheRepository
    ) {
        $this->apiTranslator = $apiTranslator;
        $this->cacheRepository = $cacheRepository;
    }

    /**
     * @param GetID  $object
     * @param string $key
     * @param null   $locale
     *
     * @return string|null
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

        /**
         * Return cached result
         * If no result, create jobs to refresh forever cache and return from forever cache immediately.
         * If no result, create forever cache.
         */
        return $this->cacheRepository->remember(
            sprintf('%s:%s:%s:%s:%s', RedisKeys::TRANSLATOR_API_WARM, get_class($object), $object->getID(), $key, $locale),
            self::CACHE_TIME,
            function () use ($object, $key, $locale) {
                $cacheKey = sprintf(
                    '%s:%s:%s:%s:%s', RedisKeys::TRANSLATOR_API, get_class($object), $object->getID(), $key, $locale
                );

                /**
                 * Do not request cache refresh on first request.
                 */
                if ($this->cacheRepository->has($cacheKey)) {
                    /**
                     * If warm cache expired, request new version of data for forever and warm cache.
                     */
                    CacheRefresh::dispatch($object, $key, $locale)->delay(now()->addMinute())->afterResponse();
                }

                /**
                 * Keep cache forever, so there is always fast cached localization available.
                 * It must be updated only in CacheRefresh job in the independent process, to keep user experience smooth.
                 */
                return $this->cacheRepository->rememberForever(
                    $cacheKey,
                    function () use ($object, $key, $locale) {
                        return $this->apiTranslator->get($object, $key, $locale);
                    });
            });
    }
}
