<?php

declare(strict_types=1);

namespace App\Service\Payment\Stripe\Config;

trait SetArrayFieldTrait
{
    protected function setArrayField(?array &$arr, string $key, mixed $value): void
    {
        if (null === $value) {
            if (null !== $arr) {
                unset($arr[$key]);
                if (empty($arr)) {
                    $arr = null;
                }
            }
        } else {
            if (null === $arr) {
                $arr = [];
            }
            /** @psalm-suppress MixedAssignment */
            $arr[$key] = $value;
        }
    }
}
