<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\UpdateItemRequest;
use PHPUnit\Framework\TestCase;

class UpdateItemRequestTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $id = random_int(1, PHP_INT_MAX);
        $name = bin2hex(random_bytes(16));
        $sku = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $manufacturerId = random_int(1, PHP_INT_MAX);
        $cost = random_int(1, PHP_INT_MAX);
        $quantity = random_int(1, PHP_INT_MAX);
        $availabilityId = random_int(1, PHP_INT_MAX);
        $weight = bin2hex(random_bytes(16));
        $length = bin2hex(random_bytes(16));
        $width = bin2hex(random_bytes(16));
        $height = bin2hex(random_bytes(16));
        $isProduct = (bool) random_int(0, 1);
        $isViewable = (bool) random_int(0, 1);
        $isPurchasable = (bool) random_int(0, 1);
        $isSpecial = (bool) random_int(0, 1);
        $isNew = (bool) random_int(0, 1);
        $chargeTax = (bool) random_int(0, 1);
        $chargeShipping = (bool) random_int(0, 1);
        $isFreeShipping = (bool) random_int(0, 1);
        $freightQuoteRequired = (bool) random_int(0, 1);
        $categoryIds = [random_int(1, PHP_INT_MAX), random_int(1, PHP_INT_MAX)];
        $imageIds = [random_int(1, PHP_INT_MAX), random_int(1, PHP_INT_MAX)];
        $dto = new UpdateItemRequest(
            $id,
            $name,
            $sku,
            $description,
            $manufacturerId,
            $cost,
            $quantity,
            $availabilityId,
            $weight,
            $length,
            $width,
            $height,
            $isProduct,
            $isViewable,
            $isPurchasable,
            $isSpecial,
            $isNew,
            $chargeTax,
            $chargeShipping,
            $isFreeShipping,
            $freightQuoteRequired,
            $categoryIds,
            $imageIds,
        );

        $this->assertSame([
            'id' => $id,
            'name' => $name,
            'sku' => $sku,
            'description' => $description,
            'manufacturerId' => $manufacturerId,
            'cost' => $cost,
            'quantity' => $quantity,
            'availabilityId' => $availabilityId,
            'weight' => $weight,
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'isProduct' => $isProduct,
            'isViewable' => $isViewable,
            'isPurchasable' => $isPurchasable,
            'isSpecial' => $isSpecial,
            'isNew' => $isNew,
            'chargeTax' => $chargeTax,
            'chargeShipping' => $chargeShipping,
            'isFreeShipping' => $isFreeShipping,
            'freightQuoteRequired' => $freightQuoteRequired,
            'categoryIds' => $categoryIds,
            'imageIds' => $imageIds,
        ], $dto->jsonSerialize());
    }
}
