<?php


namespace App\Core\Infrastructure\Services\ApiTranslator;


use App\Models\Interfaces\GetID;

interface TranslatorInterface
{
    public function get(GetID $object, string $key, $locale = null): ?string;
}
