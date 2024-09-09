<?php

declare(strict_types=1);

namespace App\Service\Payment\Stripe\Config;

class CustomerSessionConfig implements ConfigInterface
{
    private ?SessionComponentInterface $sessionComponent = null;

    public function setComponentConfig(?SessionComponentInterface $sessionComponent): static
    {
        $this->sessionComponent = $sessionComponent;

        return $this;
    }

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
