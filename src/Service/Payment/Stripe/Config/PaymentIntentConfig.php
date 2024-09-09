<?php

declare(strict_types=1);

namespace App\Service\Payment\Stripe\Config;

class PaymentIntentConfig implements ConfigInterface
{
    use SetArrayFieldTrait;

    public const string CANCELLATION_REASON_ABANDONED = 'abandoned';
    public const string CURRENCY_DEFAULT = 'usd';
    public const string FUTURE_USAGE_OFF_SESSION = 'off_session';

    private ?int $amount = null;
    private ?string $cancellationReason = null;
    private ?array $cardPaymentMethodOptions = null;
    private ?string $currency = null;
    private ?string $customerId = null;
    private ?string $description = null;
    private ?array $metadata = null;
    private ?string $paymentMethod = null;
    private ?string $setupFutureUsage = null;

    public function setAmount(?int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function setCancellationReason(?string $cancellationReason): static
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }

    public function setCardPaymentMethodOption(string $key, mixed $value): static
    {
        $this->setArrayField($this->cardPaymentMethodOptions, $key, $value);

        return $this;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function setCustomerId(?string $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setMetadata(string $key, ?string $value): static
    {
        $this->setArrayField($this->metadata, $key, $value);

        return $this;
    }

    public function setPaymentMethod(?string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function setSetupFutureUsage(?string $setupFutureUsage): static
    {
        $this->setupFutureUsage = $setupFutureUsage;

        return $this;
    }

    public function toArray(): array
    {
        $config = [];
        if (null !== $this->amount) {
            $config['amount'] = $this->amount;
        }
        if (null !== $this->cancellationReason) {
            $config['cancellation_reason'] = $this->cancellationReason;
        }
        if (null !== $this->currency) {
            $config['currency'] = $this->currency;
        }
        if (null !== $this->customerId) {
            $config['customer'] = $this->customerId;
        }
        if (null !== $this->description) {
            $config['description'] = $this->description;
        }
        if (null !== $this->metadata) {
            $config['metadata'] = $this->metadata;
        }
        if (null !== $this->paymentMethod) {
            $config['payment_method'] = $this->paymentMethod;
        }
        if (null !== $this->cardPaymentMethodOptions) {
            $config['payment_method_options'] = [
                'card' => $this->cardPaymentMethodOptions,
            ];
        }
        if (null !== $this->setupFutureUsage) {
            $config['setup_future_usage'] = $this->setupFutureUsage;
        }

        return $config;
    }
}
