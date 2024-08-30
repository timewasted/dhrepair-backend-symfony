<?php

declare(strict_types=1);

namespace App\Tests\helpers\Normalizer;

use App\Normalizer\ApiRequestNormalizer;

class TestApiRequestNormalizer extends ApiRequestNormalizer
{
    public function getPropertyAttributes(string $type): array
    {
        return parent::getPropertyAttributes($type);
    }
}
