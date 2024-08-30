<?php

declare(strict_types=1);

namespace App\Exception\JsonValidation;

use Opis\JsonSchema\Errors\ValidationError;

class JsonValidationException extends \Exception
{
    private ?ValidationError $error = null;

    public function getError(): ?ValidationError
    {
        return $this->error;
    }

    public function setError(ValidationError $error): static
    {
        $this->error = $error;

        return $this;
    }
}
