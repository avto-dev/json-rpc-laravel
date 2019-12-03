<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Factories;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tarampampam\Wrappers\Json;
use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Errors\ParseError;
use AvtoDev\JsonRpc\Errors\ServerError;
use AvtoDev\JsonRpc\Errors\InternalError;
use AvtoDev\JsonRpc\Errors\ErrorInterface;
use AvtoDev\JsonRpc\Requests\ErroredRequest;
use AvtoDev\JsonRpc\Responses\ErrorResponse;
use AvtoDev\JsonRpc\Factories\RequestFactory;
use AvtoDev\JsonRpc\Responses\ResponsesStack;
use AvtoDev\JsonRpc\Errors\InvalidParamsError;
use AvtoDev\JsonRpc\Responses\SuccessResponse;
use AvtoDev\JsonRpc\Errors\InvalidRequestError;
use AvtoDev\JsonRpc\Errors\MethodNotFoundError;
use AvtoDev\JsonRpc\Factories\FactoryInterface;
use AvtoDev\JsonRpc\Responses\ResponseInterface;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * @covers \AvtoDev\JsonRpc\Factories\RequestFactory<extended>
 */
class RequestFactoryTest extends AbstractTestCase
{
    /**
     * @var RequestFactory
     */
    protected $factory;

    /**
     * @coversNothing
     *
     * @return void
     */
    public function testInterfaces(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    /**
     * @return void
     */
    public function testSuccessResponseToJsonString(): void
    {
        $response = new SuccessResponse(Str::random(), Str::random());

        $this->assertJsonStringEqualsJsonString(Json::encode([
            'jsonrpc' => '2.0',
            'result'  => $response->getResult(),
            'id'      => $response->getId(),
        ]), $this->factory->successResponseToJsonString($response));
    }

    /**
     * @return void
     */
    public function testResponsesStackToJsonString(): void
    {
        $this->assertSame(
            ['jsonrpc', 'result', 'id'],
            \array_keys(
                Json::decode($this->factory->responsesStackToJsonString(new ResponsesStack(false, [
                    new SuccessResponse(Str::random(), null),
                ])))
            )
        );

        $responses = Json::decode($this->factory->responsesStackToJsonString(new ResponsesStack(true, [
            new SuccessResponse(Str::random(), null),
            new ErrorResponse(Str::random(), new MethodNotFoundError),
        ])));

        foreach ($responses as $response) {
            $this->assertArrayHasKey('jsonrpc', $response);
            $this->assertArrayHasKey('id', $response);
        }

        $this->assertNull($this->factory->responsesStackToJsonString(new ResponsesStack(true)));
    }

    /**
     * @return void
     */
    public function testResponsesToHttpResponse(): void
    {
        $response = $this->factory->responsesToHttpResponse(new ResponsesStack(false, [
            new SuccessResponse(Str::random(), null),
        ]));

        $this->assertSame(HttpResponse::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    /**
     * @return void
     */
    public function testResponsesToHttpResponseWithEmptyResponsesStack(): void
    {
        $response = $this->factory->responsesToHttpResponse(new ResponsesStack(true));

        $this->assertSame(HttpResponse::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $this->assertInternalType('string', $response->getContent(), '');
        $this->assertEmpty($response->getContent(), '');
    }

    /**
     * @return void
     */
    public function testErrorResponseToJsonString(): void
    {
        $response = Arr::dot(
            Json::decode($this->factory->errorResponseToJsonString(
                new ErrorResponse(Str::random(), new MethodNotFoundError)
            ))
        );

        $this->assertSame(
            ['jsonrpc', 'error.code', 'error.message', 'id'],
            \array_keys($response)
        );
    }

    /**
     * @return void
     */
    public function testErrorResponseToJsonStringWithExceptionRendering(): void
    {
        $response = Arr::dot(
            Json::decode($this->factory->errorResponseToJsonString(
                new ErrorResponse(Str::random(), new MethodNotFoundError(null, 0, new \Exception))
            ))
        );
        $this->assertSame(
            [
                'jsonrpc',
                'error.code',
                'error.message',
                'error.data.type',
                'error.data.class',
                'error.data.message',
                'error.data.code',
                'id',
            ],
            \array_keys($response)
        );

        $this->assertNull($response['error']['data']['trace'] ?? null);
    }

    /**
     * @return void
     */
    public function testErrorToHttpResponse(): void
    {
        $response = $this->factory->errorToHttpResponse(new ErrorResponse(Str::random(), new MethodNotFoundError));

        $this->assertSame(HttpResponse::HTTP_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    /**
     * @return void
     */
    public function testErrorCodeConverting(): void
    {
        $map = [
            InternalError::class       => -32603,
            InvalidParamsError::class  => -32602,
            InvalidRequestError::class => -32600,
            MethodNotFoundError::class => -32601,
            ParseError::class          => -32700,
            ServerError::class         => -32099,
        ];

        foreach ($map as $error_code => $code) {
            $response = $this->factory->errorResponseToJsonString(new ErrorResponse(null, new $error_code(null)));

            $this->assertSame($code, Json::decode($response)['error']['code'] ?? null);
        }
    }

    /**
     * @return void
     */
    public function testErrorCodeConvertingUsingCustomError(): void
    {
        $error = new class('foo', 123) extends \Exception implements ErrorInterface {
            public function getData(): void
            {
            }
        };

        $response = $this->factory->errorResponseToJsonString(new ErrorResponse(null, $error));
        $this->assertSame(123, Json::decode($response)['error']['code'] ?? null);
    }

    /**
     * @return void
     */
    public function testJsonStringToRequestsStackThrowErrorOnInvalidJson(): void
    {
        $this->expectException(ParseError::class);

        $this->factory->jsonStringToRequestsStack('{"foo":]');
    }

    /**
     * @return dump(123);void
     */
    public function testJsonStringToRequestsStackThrowErrorOnEmptyArray(): void
    {
        $this->expectException(InvalidRequestError::class);

        $this->factory->jsonStringToRequestsStack('[]');
    }

    /**
     * @return void
     */
    public function testJsonStringToRequestsStackParamsGetter(): void
    {
        $response = $this->factory->jsonStringToRequestsStack(
            '{"jsonrpc":"2.0","method":"foo","params":[1,2],"id":123}'
        );
        $this->assertSame([1, 2], $response->first()->getParams());
        $this->assertSame(123, $response->first()->getId());

        $response = $this->factory->jsonStringToRequestsStack(
            '{"jsonrpc":"2.0","method":"foo","params":{"a":1,"b":2},"id":321}'
        );
        $this->assertEquals((object) ['a' => 1, 'b' => 2], $response->first()->getParams());
        $this->assertSame(321, $response->first()->getId());
    }

    /**
     * @return void
     */
    public function testJsonStringToRequestsStackErrorRequestPassed(): void
    {
        $response = $this->factory->jsonStringToRequestsStack(
            '[1, 2, {"jsonrpc":"2.0","method":"foo","id":1}]'
        );
        $this->assertCount(3, $response);
        $this->assertInstanceOf(ErroredRequest::class, $response->all()[0]);
        $this->assertInstanceOf(ErroredRequest::class, $response->all()[1]);
        $this->assertInstanceOf(Request::class, $response->all()[2]);
    }

    /**
     * @return void
     */
    public function testResponseToJsonStringException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('~unsupported.*response~i');

        $this->factory->responseToJsonString($this->instanceUnsupportedType());
    }

    /**
     * @return ResponseInterface
     */
    protected function instanceUnsupportedType(): ResponseInterface
    {
        return new class implements ResponseInterface {
            public function getId(): void
            {
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->app->make(RequestFactory::class);
    }
}
