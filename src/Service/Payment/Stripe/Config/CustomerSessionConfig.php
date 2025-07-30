<?php

declare(strict_types=1);

namespace App\Service\Payment\Stripe\Config;

final class CustomerSessionConfig implements ConfigInterface
{
    private ?SessionComponentInterface $sessionComponent = null;

    public function setComponentConfig(?SessionComponentInterface $sessionComponent): CustomerSessionConfig
    {
        $this->sessionComponent = $sessionComponent;

        return $this;
    }

    #[\Override]
    public function toArray(): array
    {
        $config = [];
        if (null !== $this->sessionComponent) {
            $sessionConfig = $this->sessionComponent->toArray();
            if (!empty($sessionConfig)) {
                $config['components'] = $sessionConfig;
            }
        }

        return $config;
    }
}
