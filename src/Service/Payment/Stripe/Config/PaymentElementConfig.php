<?php

declare(strict_types=1);

namespace App\Service\Payment\Stripe\Config;

class PaymentElementConfig implements ConfigInterface, SessionComponentInterface
{
    public const string COMPONENT_NAME = 'payment_element';

    public const string FEATURE_PAYMENT_METHOD_REDISPLAY = 'payment_method_redisplay';
    public const string FEATURE_PAYMENT_METHOD_REMOVE = 'payment_method_remove';
    public const string FEATURE_PAYMENT_METHOD_SAVE = 'payment_method_save';
    public const string FEATURE_PAYMENT_METHOD_SAVE_USAGE = 'payment_method_save_usage';

    public const string IS_DISABLED = 'disabled';
    public const string IS_ENABLED = 'enabled';

    private bool $enabled = false;
    private array $features = [];

    public function setFeaturePaymentMethodRedisplayEnabled(bool $enabled): static
    {
        $this->features[self::FEATURE_PAYMENT_METHOD_REDISPLAY] = $enabled ? self::IS_ENABLED : self::IS_DISABLED;

        return $this;
    }

    public function setFeaturePaymentMethodRemoveEnabled(bool $enabled): static
    {
        $this->features[self::FEATURE_PAYMENT_METHOD_REMOVE] = $enabled ? self::IS_ENABLED : self::IS_DISABLED;

        return $this;
    }

    public function setFeaturePaymentMethodSaveEnabled(bool $enabled): static
    {
        $this->features[self::FEATURE_PAYMENT_METHOD_SAVE] = $enabled ? self::IS_ENABLED : self::IS_DISABLED;

        return $this;
    }

    public function setFeaturePaymentMethodSaveUsageEnabled(bool $enabled): static
    {
        $this->features[self::FEATURE_PAYMENT_METHOD_SAVE_USAGE] = $enabled ? self::IS_ENABLED : self::IS_DISABLED;

        return $this;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function toArray(): array
    {
        $config = [
            self::COMPONENT_NAME => [
                'enabled' => $this->enabled,
                'features' => $this->features,
            ],
        ];

        return $config;
    }
}
