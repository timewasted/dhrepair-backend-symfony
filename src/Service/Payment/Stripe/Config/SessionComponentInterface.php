<?php

declare(strict_types=1);

namespace App\Service\Payment\Stripe\Config;

interface SessionComponentInterface
{
    public function toArray(): array;
}
