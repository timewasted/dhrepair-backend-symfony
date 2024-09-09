<?php

declare(strict_types=1);

namespace App\Service\Payment\Stripe\Config;

class CustomerConfig implements ConfigInterface
{
    use SetArrayFieldTrait;

    private ?string $email = null;
    private ?array $metadata = null;
    private ?array $shippingAddress = null;

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function setMetadata(string $key, ?string $value): static
    {
        $this->setArrayField($this->metadata, $key, $value);

        return $this;
    }

    public function setShippingAddressLine1(?string $value): static
    {
        $this->setArrayField($this->shippingAddress, 'line1', $value);

        return $this;
    }

    public function setShippingAddressLine2(?string $value): static
    {
        $this->setArrayField($this->shippingAddress, 'line2', $value);

        return $this;
    }

    public function setShippingAddressCity(?string $value): static
    {
        $this->setArrayField($this->shippingAddress, 'city', $value);

        return $this;
    }

    public function setShippingAddressState(?string $value): static
    {
        $this->setArrayField($this->shippingAddress, 'state', $value);

        return $this;
    }

    public function setShippingAddressPostalCode(?string $value): static
    {
        $this->setArrayField($this->shippingAddress, 'postal_code', $value);

        return $this;
    }

    public function setShippingAddressCountry(?string $value): static
    {
        $this->setArrayField($this->shippingAddress, 'country', $value);

        return $this;
    }

    public function toArray(): array
    {
        $config = [];
        if (null !== $this->email) {
            $config['email'] = $this->email;
        }
        if (null !== $this->metadata) {
            $config['metadata'] = $this->metadata;
        }
        if (null !== $this->shippingAddress) {
            $config['shipping'] = [
                'address' => $this->shippingAddress,
            ];
        }

        return $config;
    }
}
