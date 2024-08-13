<?php

declare(strict_types=1);

namespace App\Attribute;

use App\Exception\JsonValidation\DataPathNotFoundException;
use App\Exception\JsonValidation\InvalidJsonException;
use App\Exception\JsonValidation\JsonValidationException;
use App\Exception\JsonValidation\SchemaNotFoundException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;
use Symfony\Component\HttpFoundation\Request;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class JsonValidation
{
    private ?Request $request = null;
    private ?Validator $validator = null;

    private string $schema;
    private string $schemaPrefix = '';
    /**
     * @var list<string>
     */
    private array $dataPath;
    private ?array $globals;
    private ?array $slots;

    public function __construct(
        ?string $schema = null,
        ?string $dataPath = null,
        ?array $globals = null,
        ?array $slots = null
    ) {
        if (null === $schema) {
            throw new \InvalidArgumentException('Schema cannot be null');
        }
        $this->schema = $schema;

        $this->setDataPath($dataPath);
        $this->globals = $globals;
        $this->slots = $slots;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function setSchemaPrefix(string $prefix): static
    {
        $this->schemaPrefix = $prefix;

        return $this;
    }

    public function setValidator(Validator $validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @throws JsonValidationException
     */
    public function validate(): void
    {
        if (null === $this->request) {
            throw new \LogicException('Request must be supplied before attempting validation');
        }
        if (null === $this->validator) {
            $this->validator = new Validator();
        }

        if ('body' === $this->dataPath[0]) {
            /** @psalm-suppress MixedAssignment */
            $requestData = $this->getDataFromJsonString($this->request->getContent(), 1);
        } elseif ($this->request->query->has($this->dataPath[1])) {
            /**
             * @psalm-suppress MixedAssignment
             * @psalm-suppress PossiblyNullArgument
             */
            $requestData = $this->getDataFromJsonString($this->request->query->get($this->dataPath[1]), 2);
        } else {
            throw new DataPathNotFoundException();
        }

        try {
            $result = $this->validator->validate($requestData, $this->schemaPrefix.$this->schema, $this->globals, $this->slots);
        } catch (\RuntimeException $e) {
            if (0 === strncasecmp($e->getMessage(), 'Schema not found:', 17)) {
                throw new SchemaNotFoundException($e->getMessage(), (int) $e->getCode(), $e);
            }
            throw new JsonValidationException($e->getMessage(), (int) $e->getCode(), $e);
        }
        if (!$result->isValid()) {
            $errorFormatter = new ErrorFormatter();
            /** @var ValidationError $error */
            $error = $result->error();
            throw new JsonValidationException($errorFormatter->formatErrorMessage($error));
        }
    }

    private function dataPathNotFoundException(string $messageFmt, int $dataPathOffset): DataPathNotFoundException
    {
        return new DataPathNotFoundException(sprintf($messageFmt, implode('.', array_slice($this->dataPath, 0, $dataPathOffset + 1)), implode('.', $this->dataPath)));
    }

    /**
     * @throws JsonValidationException
     */
    private function getDataFromJsonString(string $jsonData, int $dataPathOffset): mixed
    {
        if ('' === $jsonData) {
            throw new InvalidJsonException();
        }
        try {
            /** @psalm-suppress MixedAssignment */
            $jsonData = json_decode($jsonData, false, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidJsonException($e->getMessage(), $e->getCode(), $e);
        }

        $pathCount = count($this->dataPath) - 1;
        for ($i = $dataPathOffset; $i <= $pathCount; ++$i) {
            if (is_array($jsonData)) {
                if (!array_key_exists($this->dataPath[$i], $jsonData)) {
                    throw $this->dataPathNotFoundException('Path "%s" does not exist in "%s"', $i);
                }
                /** @psalm-suppress MixedAssignment */
                $jsonData = $jsonData[$this->dataPath[$i]];
            } elseif (is_object($jsonData)) {
                if (!property_exists($jsonData, $this->dataPath[$i])) {
                    throw $this->dataPathNotFoundException('Path "%s" does not exist in "%s"', $i);
                }
                /** @psalm-suppress MixedAssignment */
                $jsonData = $jsonData->{$this->dataPath[$i]};
            } elseif ($i !== $pathCount) {
                throw $this->dataPathNotFoundException('Cannot proceed deeper than "%s" in "%s"', $i - 1);
            }
        }

        return $jsonData;
    }

    private function setDataPath(?string $dataPath): void
    {
        if (null === $dataPath) {
            $dataPath = 'body';
        }
        $dataPathPieces = explode('.', $dataPath);
        $dataPathPieces[0] = strtolower($dataPathPieces[0]);
        if ('query' === $dataPathPieces[0]) {
            if (count($dataPathPieces) < 2) {
                throw new \InvalidArgumentException('Data path '.$dataPath.' is not valid');
            }
        } elseif ('body' !== $dataPathPieces[0]) {
            throw new \InvalidArgumentException('Data path '.$dataPath.' is not valid');
        }
        $this->dataPath = $dataPathPieces;
    }
}
