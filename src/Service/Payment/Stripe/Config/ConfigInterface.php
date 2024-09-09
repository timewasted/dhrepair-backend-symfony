<?php

declare(strict_types=1);

namespace App\Service\Payment\Stripe\Config;

interface ConfigInterface
{
    public function toArray(): array;
}
