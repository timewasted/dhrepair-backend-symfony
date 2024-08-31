<?php

declare(strict_types=1);

namespace App\Tests\helpers\Service;

use App\Service\JsonValidationService;

readonly class TestJsonValidationService extends JsonValidationService
{
    public function parseDataPath(?string $dataPath): array
    {
        return parent::parseDataPath($dataPath);
    }
}
