<?php

declare(strict_types=1);

namespace App\Http\Test\Unit;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
final class JsonResponseTest extends TestCase
{
    /**
     * @covers  \JsonResponse
     */
    public function testWithCode(): void
    {
        $response = new JsonResponse(0, 201);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('0', $response->getBody()->getContents());
        self::assertEquals(201, $response->getStatusCode());
    }

    /**
     * @dataProvider provideResponseCases
     *
     * @param mixed $source
     * @param mixed $expect
     *
     * @covers       \JsonResponse
     */
    public function testResponse($source, $expect): void
    {
        $response = new JsonResponse($source);
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals($expect, $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return array<mixed>
     */
    public static function provideResponseCases(): iterable
    {
        $object       = new stdClass();
        $object->str  = 'value';
        $object->int  = 1;
        $object->none = null;

        $array = [
            'str'  => 'value',
            'int'  => 1,
            'none' => null,
        ];

        return [
            'null'   => [null, 'null'],
            'empty'  => ['', '""'],
            'number' => [12, '12'],
            'string' => ['12', '"12"'],
            'object' => [$object, '{"str":"value","int":1,"none":null}'],
            'array'  => [$array, '{"str":"value","int":1,"none":null}'],
        ];
    }
}
