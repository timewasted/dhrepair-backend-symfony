<?php

declare(strict_types=1);

namespace App\Tests\unit\Service;

use App\Exception\JsonValidation\DataPathNotFoundException;
use App\Exception\JsonValidation\InvalidJsonException;
use App\Exception\JsonValidation\JsonValidationException;
use App\Exception\JsonValidation\SchemaNotFoundException;
use App\Tests\helpers\Service\TestJsonValidationService;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JsonValidationServiceTest extends TestCase
{
    public function testParseDataPathNotSpecifiedIsBody(): void
    {
        $service = new TestJsonValidationService($this->createMock(Validator::class));

        $this->assertSame(['body'], $service->parseDataPath(null));
    }

    public function testParseDataPathBody(): void
    {
        $service = new TestJsonValidationService($this->createMock(Validator::class));

        $this->assertSame(['body'], $service->parseDataPath('body'));
    }

    public function testParseDataPathBodyWithElementPath(): void
    {
        $service = new TestJsonValidationService($this->createMock(Validator::class));

        $this->assertSame([
            'body',
            'with',
            'deeper',
            'path',
        ], $service->parseDataPath('body.with.deeper.path'));
    }

    public function testParseDataPathQueryIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Data path query is not valid');

        (new TestJsonValidationService($this->createMock(Validator::class)))
            ->parseDataPath('query');
    }

    public function testParseDataPathQueryWithElementPath(): void
    {
        $service = new TestJsonValidationService($this->createMock(Validator::class));

        $this->assertSame([
            'query',
            'parameter',
        ], $service->parseDataPath('query.parameter'));
    }

    public function testParseDataPathInvalidBase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Data path invalid is not valid');

        (new TestJsonValidationService($this->createMock(Validator::class)))
            ->parseDataPath('invalid');
    }

    public function testValidateSuccessBodyObject(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'object' => true,
        ]);
        $request = $this->createRequest('/', $requestData);
        $validator = $this->getSuccessValidator($requestData, $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'body'));
    }

    public function testValidateSuccessBodyArray(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode(['a', 'b', 'c']);
        $request = $this->createRequest('/', $requestData);
        $validator = $this->getSuccessValidator($requestData, $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'body'));
    }

    public function testValidateSuccessBodyScalar(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode('foobar');
        $request = $this->createRequest('/', $requestData);
        $validator = $this->getSuccessValidator($requestData, $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'body'));
    }

    public function testValidateSuccessBodyElementPathObject(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                'nested' => [
                    'value' => [
                        'object' => true,
                    ],
                ],
            ],
        ]);
        $request = $this->createRequest('/', $requestData);
        $validator = $this->getSuccessValidator(json_encode(['object' => true]), $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'body.a.nested.value'));
    }

    public function testValidateSuccessBodyElementPathArray(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                'nested' => [
                    'value' => ['a', 'b', 'c'],
                ],
            ],
        ]);
        $request = $this->createRequest('/', $requestData);
        $validator = $this->getSuccessValidator(json_encode(['a', 'b', 'c']), $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'body.a.nested.value'));
    }

    public function testValidateSuccessBodyElementPathScalar(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                'nested' => [
                    'value' => 'foobar',
                ],
            ],
        ]);
        $request = $this->createRequest('/', $requestData);
        $validator = $this->getSuccessValidator(json_encode('foobar'), $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'body.a.nested.value'));
    }

    public function testValidateSuccessBodyObjectsAndArrays(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                [],
                [
                    'b' => [
                        'c' => ['1', '2', '3'],
                    ],
                ],
            ],
        ]);
        $request = $this->createRequest('/', $requestData);
        $validator = $this->getSuccessValidator(json_encode('3'), $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'body.a.1.b.c.2'));
    }

    public function testValidateSuccessQueryObject(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'object' => true,
        ]);
        $request = $this->createRequest('/?foo='.$requestData);
        $validator = $this->getSuccessValidator($requestData, $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'query.foo'));
    }

    public function testValidateSuccessQueryArray(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode(['a', 'b', 'c']);
        $request = $this->createRequest('/?foo='.$requestData);
        $validator = $this->getSuccessValidator($requestData, $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'query.foo'));
    }

    public function testValidateSuccessQueryScalar(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode('foobar');
        $request = $this->createRequest('/?foo='.$requestData);
        $validator = $this->getSuccessValidator($requestData, $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'query.foo'));
    }

    public function testValidateSuccessQueryElementPathObject(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                'nested' => [
                    'value' => [
                        'object' => true,
                    ],
                ],
            ],
        ]);
        $request = $this->createRequest('/?foo='.$requestData);
        $validator = $this->getSuccessValidator(json_encode(['object' => true]), $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'query.foo.a.nested.value'));
    }

    public function testValidateSuccessQueryElementPathArray(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                'nested' => [
                    'value' => ['a', 'b', 'c'],
                ],
            ],
        ]);
        $request = $this->createRequest('/?foo='.$requestData);
        $validator = $this->getSuccessValidator(json_encode(['a', 'b', 'c']), $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'query.foo.a.nested.value'));
    }

    public function testValidateSuccessQueryElementPathScalar(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                'nested' => [
                    'value' => 'foobar',
                ],
            ],
        ]);
        $request = $this->createRequest('/?foo='.$requestData);
        $validator = $this->getSuccessValidator(json_encode('foobar'), $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'query.foo.a.nested.value'));
    }

    public function testValidateSuccessQueryObjectsAndArrays(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                [],
                [
                    'b' => [
                        'c' => ['1', '2', '3'],
                    ],
                ],
            ],
        ]);
        $request = $this->createRequest('/?foo='.$requestData);
        $validator = $this->getSuccessValidator(json_encode('3'), $schema);
        $service = new TestJsonValidationService($validator);

        $this->assertTrue($service->validate($request, $schema, 'query.foo.a.1.b.c.2'));
    }

    public function testValidateFailureNoJsonData(): void
    {
        $this->expectException(InvalidJsonException::class);

        $schema = '/schema-file.json';
        $requestData = '';
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body');
    }

    public function testValidateFailureInvalidJsonData(): void
    {
        $this->expectException(InvalidJsonException::class);

        $schema = '/schema-file.json';
        $requestData = 'invalid-json';
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body');
    }

    public function testValidateFailureQueryParamNotFound(): void
    {
        $this->expectException(DataPathNotFoundException::class);
        $this->expectExceptionMessage('');

        $schema = '/schema-file.json';
        $requestData = json_encode([
            'foo' => 'bar',
        ]);
        $request = $this->createRequest('/?foobar='.$requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'query.foo');
    }

    public function testValidateFailurePathDoesNotExistEarlyObject(): void
    {
        $this->expectException(DataPathNotFoundException::class);
        $this->expectExceptionMessage('Path "body.foobar" does not exist in "body.foobar.baz"');

        $schema = '/schema-file.json';
        $requestData = json_encode([
            'foo' => 'bar',
        ]);
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body.foobar.baz');
    }

    public function testValidateFailurePathDoesNotExistLateObject(): void
    {
        $this->expectException(DataPathNotFoundException::class);
        $this->expectExceptionMessage('Path "body.a.deeply.nested" does not exist in "body.a.deeply.nested.value"');

        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                'deeply' => [
                    'nested-typo' => [
                        'value' => 'foobar',
                    ],
                ],
            ],
        ]);
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body.a.deeply.nested.value');
    }

    public function testValidateFailureCanNotProceedDeeperObject(): void
    {
        $this->expectException(DataPathNotFoundException::class);
        $this->expectExceptionMessage('Cannot proceed deeper than "body.a.deeply" in "body.a.deeply.nested.value"');

        $schema = '/schema-file.json';
        $requestData = json_encode([
            'a' => [
                'deeply' => 'foo',
            ],
        ]);
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body.a.deeply.nested.value');
    }

    public function testValidateFailurePathDoesNotExistEarlyArray(): void
    {
        $this->expectException(DataPathNotFoundException::class);
        $this->expectExceptionMessage('Path "body.2" does not exist in "body.2.foo"');

        $schema = '/schema-file.json';
        $requestData = json_encode(['a', 'b']);
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body.2.foo');
    }

    public function testValidateFailurePathDoesNotExistLateArray(): void
    {
        $this->expectException(DataPathNotFoundException::class);
        $this->expectExceptionMessage('Path "body.0.1.2.3" does not exist in "body.0.1.2.3"');

        $schema = '/schema-file.json';
        $requestData = json_encode([
            [
                [],
                [
                    [],
                    [],
                    ['a', 'b', 'c'],
                ],
            ],
        ]);
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body.0.1.2.3');
    }

    public function testValidateFailureExceptionDuringValidationSchemaNotFound(): void
    {
        $schema = '/schema-file.json';
        $schemaNotFoundMsg = 'Schema not found: '.$schema;

        $this->expectException(SchemaNotFoundException::class);
        $this->expectExceptionMessage($schemaNotFoundMsg);

        $requestData = json_encode([
            'object' => true,
        ]);
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())->method('validate')
            ->willThrowException(new \RuntimeException($schemaNotFoundMsg));
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body');
    }

    public function testValidateFailureExceptionDuringValidation(): void
    {
        $exceptionMsg = bin2hex(random_bytes(16));

        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage($exceptionMsg);

        $schema = '/schema-file.json';
        $requestData = json_encode([
            'object' => true,
        ]);
        $request = $this->createRequest('/', $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())->method('validate')
            ->willThrowException(new \RuntimeException($exceptionMsg));
        $service = new TestJsonValidationService($validator);

        $service->validate($request, $schema, 'body');
    }

    public function testValidateFailureNoErrorInException(): void
    {
        $this->expectException(JsonValidationException::class);

        $schema = '/schema-file.json';
        $requestData = json_encode([
            'object' => true,
        ]);
        $request = $this->createRequest('/', $requestData);

        $validationResult = $this->createMock(ValidationResult::class);
        $validationResult->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $validationResult->expects($this->once())
            ->method('error')
            ->willReturn(null);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())->method('validate')
            ->willReturn($validationResult);
        $service = new TestJsonValidationService($validator);

        try {
            $service->validate($request, $schema, 'body');
        } catch (JsonValidationException $exception) {
            $this->assertNull($exception->getError());
            throw $exception;
        }
    }

    public function testValidateFailureErrorInException(): void
    {
        $this->expectException(JsonValidationException::class);

        $schema = '/schema-file.json';
        $requestData = json_encode([
            'object' => true,
        ]);
        $request = $this->createRequest('/', $requestData);

        $validationError = $this->createMock(ValidationError::class);

        $validationResult = $this->createMock(ValidationResult::class);
        $validationResult->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $validationResult->expects($this->once())
            ->method('error')
            ->willReturn($validationError);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())->method('validate')
            ->willReturn($validationResult);
        $service = new TestJsonValidationService($validator);

        try {
            $service->validate($request, $schema, 'body');
        } catch (JsonValidationException $exception) {
            $this->assertSame($validationError, $exception->getError());
            throw $exception;
        }
    }

    private function createRequest(string $url, ?string $requestData = null): Request
    {
        return Request::create($url, 'GET', [], [], [], [], $requestData);
    }

    private function getSuccessfulValidationResult(): ValidationResult
    {
        $validationResult = $this->createMock(ValidationResult::class);
        $validationResult->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        return $validationResult;
    }

    private function getSuccessValidator(string $requestData, string $schema): Validator
    {
        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with(json_decode($requestData, false), $schema, null, null)
            ->willReturn($this->getSuccessfulValidationResult());

        return $validator;
    }
}
