<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\JsonValidation\DataPathNotFoundException;
use App\Exception\JsonValidation\InvalidJsonException;
use App\Exception\JsonValidation\JsonValidationException;
use App\Exception\JsonValidation\SchemaNotFoundException;
use Opis\JsonSchema\Validator;
use Symfony\Component\HttpFoundation\Request;

readonly class JsonValidationService
{
    public function __construct(
        private Validator $validator,
        private string $schemaPrefix = '',
    ) {
    }

    /**
     * @throws JsonValidationException
     */
    public function validate(Request $request, string $schema, ?string $dataPath, ?array $globals = null, ?array $slots = null): bool
    {
        $dataPath = $this->parseDataPath($dataPath);
        if ('body' === $dataPath[0]) {
            /** @psalm-suppress MixedAssignment */
            $requestData = $this->getDataFromJsonString($request->getContent(), $dataPath, 1);
        } elseif ($request->query->has($dataPath[1])) {
            /**
             * @psalm-suppress MixedAssignment
             * @psalm-suppress PossiblyNullArgument
             */
            $requestData = $this->getDataFromJsonString($request->query->get($dataPath[1]), $dataPath, 2);
        } else {
            throw new DataPathNotFoundException();
        }

        try {
            $result = $this->validator->validate($requestData, $this->schemaPrefix.$schema, $globals, $slots);
        } catch (\RuntimeException $e) {
            if (0 === strncasecmp($e->getMessage(), 'Schema not found:', 17)) {
                throw new SchemaNotFoundException($e->getMessage(), (int) $e->getCode(), $e);
            }
            throw new JsonValidationException($e->getMessage(), (int) $e->getCode(), $e);
        }
        if (!$result->isValid()) {
            $exception = new JsonValidationException();
            if (null !== ($error = $result->error())) {
                $exception->setError($error);
            }
            throw $exception;
        }

        return true;
    }

    /**
     * @param list<string> $dataPath
     */
    private function dataPathNotFoundException(string $messageFmt, array $dataPath, int $dataPathOffset): DataPathNotFoundException
    {
        return new DataPathNotFoundException(sprintf($messageFmt, implode('.', array_slice($dataPath, 0, $dataPathOffset + 1)), implode('.', $dataPath)));
    }

    /**
     * @param list<string> $dataPath
     *
     * @throws JsonValidationException
     */
    private function getDataFromJsonString(string $jsonData, array $dataPath, int $dataPathOffset): mixed
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

        $pathCount = count($dataPath) - 1;
        for ($i = $dataPathOffset; $i <= $pathCount; ++$i) {
            if (is_array($jsonData)) {
                if (!array_key_exists($dataPath[$i], $jsonData)) {
                    throw $this->dataPathNotFoundException('Path "%s" does not exist in "%s"', $dataPath, $i);
                }
                /** @psalm-suppress MixedAssignment */
                $jsonData = $jsonData[$dataPath[$i]];
            } elseif (is_object($jsonData)) {
                if (!property_exists($jsonData, $dataPath[$i])) {
                    throw $this->dataPathNotFoundException('Path "%s" does not exist in "%s"', $dataPath, $i);
                }
                /** @psalm-suppress MixedAssignment */
                $jsonData = $jsonData->{$dataPath[$i]};
            } elseif ($i !== $pathCount) {
                throw $this->dataPathNotFoundException('Cannot proceed deeper than "%s" in "%s"', $dataPath, $i - 1);
            }
        }

        return $jsonData;
    }

    /**
     * @return list<string>
     */
    protected function parseDataPath(?string $dataPath): array
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

        return $dataPathPieces;
    }
}
