<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Http\Controllers;

use AvtoDev\JsonRpc\Kernel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tarampampam\Wrappers\Json;
use AvtoDev\JsonRpc\Router\Router;
use AvtoDev\JsonRpc\Errors\ParseError;
use AvtoDev\JsonRpc\Errors\ServerError;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\Factories\RequestFactory;
use AvtoDev\JsonRpc\Http\Controllers\RpcController;

/**
 * @covers \AvtoDev\JsonRpc\Http\Controllers\RpcController<extended>
 */
class RpcControllerTest extends AbstractTestCase
{
    /**
     * @var RpcController
     */
    protected $controller;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var Router
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new RpcController;
        $this->router     = $this->app->make(Router::class);

        $this->kernel = $this->app->make(Kernel::class, ['router' => $this->router]);
    }

    /**
     * Test with Parse Error.
     *
     * @return void
     */
    public function testIndexWithParseError(): void
    {
        $this->expectException(ParseError::class);

        $this->controller->index($this->createRequest(), new RequestFactory, $this->kernel);
    }

    /**
     * Test with single success response.
     *
     * @return void
     */
    public function testIndexWithSuccessResponse(): void
    {
        $this->router->on($method = 'foo', function () {
            return true;
        });
        $request = $this->createRequest(Json::encode([
            'jsonrpc' => '2.0',
            'method'  => $method,
            'params'  => [],
            'id'      => $request_id = Str::random(),
        ]));
        /** @var Response $response */
        $response = $this->controller->index($request, new RequestFactory, $this->kernel);

        $this->assertSame(
            Json::encode(['jsonrpc' => '2.0', 'result' => true, 'id' => $request_id]),
            $response->getContent()
        );
    }

    /**
     * Test with batch call.
     *
     * @return void
     */
    public function testIndexBatchCallWithSuccessResponse(): void
    {
        $return_value = Str::uuid();
        $this->router->on('foo', function () use ($return_value) {
            return ['uuid' => $return_value];
        });

        $request = $this->createRequest(
            Json::encode([
                    [
                        'jsonrpc' => '2.0',
                        'method'  => 'foo',
                        'params'  => [],
                        'id'      => $first_request_id = Str::uuid(),
                    ], [
                        'jsonrpc' => '2.0',
                        'method'  => 'bar',
                        'params'  => [],
                        'id'      => $second_request_id = Str::uuid(),
                    ],
                ]
            )
        );
        /** @var Response $response */
        $response = $this->controller->index($request, new RequestFactory, $this->kernel);

        $this->assertSame(
            Json::encode([
                [
                    'jsonrpc' => '2.0',
                    'result'  => ['uuid' => $return_value],
                    'id'      => $first_request_id,
                ],
                [
                    'jsonrpc' => '2.0',
                    'error'   => ['code' => -32601, 'message' => 'Method not found'],
                    'id'      => $second_request_id,
                ],
            ]),
            $response->getContent()
        );
    }

    /**
     * Test with Error in response.
     *
     * @return void
     */
    public function testIndexWithErrorResponse(): void
    {
        $request_id = Str::uuid();
        $this->router->on($method = 'foo', function () use ($request_id): void {
            throw new ServerError('Server error occurred');
        });
        $request = $this->createRequest(Json::encode([
            'jsonrpc' => '2.0',
            'method'  => $method,
            'params'  => [],
            'id'      => $request_id,
        ]));
        /** @var Response $response */
        $response = $this->controller->index($request, new RequestFactory, $this->kernel);

        $result = Json::decode($response->getContent());
        $this->assertArrayHasKey('error', $result);
        $this->assertSame('Server error occurred', $result['error']['message']);
    }

    /**
     * Create request.
     *
     * @param string|null $content
     *
     * @return Request
     */
    protected function createRequest(?string $content = null): Request
    {
        return new Request([], [], [], [], [], [], $content);
    }
}
