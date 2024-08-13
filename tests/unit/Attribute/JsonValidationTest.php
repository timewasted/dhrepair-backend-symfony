<?php

declare(strict_types=1);

namespace App\Tests\unit\Attribute;

use App\Attribute\JsonValidation;
use App\Exception\JsonValidation\DataPathNotFoundException;
use App\Exception\JsonValidation\InvalidJsonException;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JsonValidationTest extends TestCase
{
    public function testConstructSchemaIsRequired(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Schema cannot be null');
        new JsonValidation(null);
    }

    public function testConstructDataPathNotSpecifiedIsValid(): void
    {
        new JsonValidation('schema', null);
        $this->expectNotToPerformAssertions();
    }

    public function testConstructDataPathBodyIsValid(): void
    {
        new JsonValidation('schema', 'body');
        $this->expectNotToPerformAssertions();
    }

    public function testConstructDataPathBodyWithElementPathIsValid(): void
    {
        new JsonValidation('schema', 'body.with.deeper.path');
        $this->expectNotToPerformAssertions();
    }

    public function testConstructDataPathQueryRequiresElementPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Data path query is not valid');
        new JsonValidation('schema', 'query');
    }

    public function testConstructDataPathQuerySingleElementPathIsValid(): void
    {
        new JsonValidation('schema', 'query.parameter');
        $this->expectNotToPerformAssertions();
    }

    public function testValidateRequestIsRequired(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Request must be supplied before attempting validation');
        (new JsonValidation('schema', 'body'))->validate();
    }

    public function testValidateSuccessBodyObject(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'object' => true,
        ]);
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);
        $validator = $this->getValidator($requestData, $schema);

        (new JsonValidation($schema, 'body'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateSuccessBodyArray(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode(['a', 'b', 'c']);
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);
        $validator = $this->getValidator($requestData, $schema);

        (new JsonValidation($schema, 'body'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateSuccessBodyScalar(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode('foobar');
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);
        $validator = $this->getValidator($requestData, $schema);

        (new JsonValidation($schema, 'body'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);
        $validator = $this->getValidator(json_encode(['object' => true]), $schema);

        (new JsonValidation($schema, 'body.a.nested.value'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);
        $validator = $this->getValidator(json_encode(['a', 'b', 'c']), $schema);

        (new JsonValidation($schema, 'body.a.nested.value'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);
        $validator = $this->getValidator(json_encode('foobar'), $schema);

        (new JsonValidation($schema, 'body.a.nested.value'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);
        $validator = $this->getValidator(json_encode('3'), $schema);

        (new JsonValidation($schema, 'body.a.1.b.c.2'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateSuccessQueryObject(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode([
            'object' => true,
        ]);
        $request = Request::create('/?foo='.$requestData);
        $validator = $this->getValidator($requestData, $schema);

        (new JsonValidation($schema, 'query.foo'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateSuccessQueryArray(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode(['a', 'b', 'c']);
        $request = Request::create('/?foo='.$requestData);
        $validator = $this->getValidator($requestData, $schema);

        (new JsonValidation($schema, 'query.foo'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateSuccessQueryScalar(): void
    {
        $schema = '/schema-file.json';
        $requestData = json_encode('foobar');
        $request = Request::create('/?foo='.$requestData);
        $validator = $this->getValidator($requestData, $schema);

        (new JsonValidation($schema, 'query.foo'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/?foo='.$requestData);
        $validator = $this->getValidator(json_encode(['object' => true]), $schema);

        (new JsonValidation($schema, 'query.foo.a.nested.value'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/?foo='.$requestData);
        $validator = $this->getValidator(json_encode(['a', 'b', 'c']), $schema);

        (new JsonValidation($schema, 'query.foo.a.nested.value'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/?foo='.$requestData);
        $validator = $this->getValidator(json_encode('foobar'), $schema);

        (new JsonValidation($schema, 'query.foo.a.nested.value'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/?foo='.$requestData);
        $validator = $this->getValidator(json_encode('3'), $schema);

        (new JsonValidation($schema, 'query.foo.a.1.b.c.2'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateFailureNoJsonData(): void
    {
        $this->expectException(InvalidJsonException::class);

        $schema = '/schema-file.json';
        $requestData = '';
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');

        (new JsonValidation($schema, 'body'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateFailureInvalidJsonData(): void
    {
        $this->expectException(InvalidJsonException::class);

        $schema = '/schema-file.json';
        $requestData = 'invalid-json';
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');

        (new JsonValidation($schema, 'body'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateFailurePathDoesNotExistEarlyObject(): void
    {
        $this->expectException(DataPathNotFoundException::class);
        $this->expectExceptionMessage('Path "body.foobar" does not exist in "body.foobar.baz"');

        $schema = '/schema-file.json';
        $requestData = json_encode([
            'foo' => 'bar',
        ]);
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');

        (new JsonValidation($schema, 'body.foobar.baz'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');

        (new JsonValidation($schema, 'body.a.deeply.nested.value'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');

        (new JsonValidation($schema, 'body.a.deeply.nested.value'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    public function testValidateFailurePathDoesNotExistEarlyArray(): void
    {
        $this->expectException(DataPathNotFoundException::class);
        $this->expectExceptionMessage('Path "body.2" does not exist in "body.2.foo"');

        $schema = '/schema-file.json';
        $requestData = json_encode(['a', 'b']);
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');

        (new JsonValidation($schema, 'body.2.foo'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
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
        $request = Request::create('/', 'GET', [], [], [], [], $requestData);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');

        (new JsonValidation($schema, 'body.0.1.2.3'))
            ->setRequest($request)
            ->setValidator($validator)
            ->validate();
    }

    private function getSuccessfulValidationResult(): ValidationResult
    {
        $validationResult = $this->createMock(ValidationResult::class);
        $validationResult->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        return $validationResult;
    }

    private function getValidator(string $requestData, string $schema, ?array $globals = null, ?array $slots = null): Validator
    {
        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with(json_decode($requestData, false), $schema, $globals, $slots)
            ->willReturn($this->getSuccessfulValidationResult());

        return $validator;
    }
}
