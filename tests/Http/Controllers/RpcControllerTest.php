<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Http\Controllers;

use Mockery as m;
use RuntimeException;
use Illuminate\Support\Str;
use AvtoDev\JsonRpc\KernelInterface;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\Http\Controllers\RpcController;
use Illuminate\Contracts\Routing\Registrar as HttpRegistrar;

/**
 * @covers \AvtoDev\JsonRpc\Http\Controllers\RpcController<extended>
 */
class RpcControllerTest extends AbstractTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var HttpRegistrar
     */
    protected $http_registrar;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->router         = $this->app->make(RouterInterface::class);
        $this->http_registrar = $this->app->make(HttpRegistrar::class);

        $this->http_registrar->post($uri = '/rpc', RpcController::class);
    }

    /**
     * @return void
     */
    public function testSuccessCalling(): void
    {
        $this->router->on($method = 'foo', static function (): string {
            return 'bar';
        });

        $this
            ->postJson('/rpc', [
                'jsonrpc' => 2.0,
                'method'  => $method,
                'id'      => $id = Str::random(),
            ])
            ->assertStatus(200)
            ->assertJsonStructure(['jsonrpc', 'result', 'id'])
            ->assertJsonPath('id', $id)
            ->assertJsonPath('result', 'bar');
    }

    /**
     * @return void
     */
    public function testMissingMethodCalling(): void
    {
        $this
            ->postJson('/rpc', [
                'jsonrpc' => 2.0,
                'method'  => Str::random(),
                'id'      => $id = Str::random(),
            ])
            ->assertStatus(200)
            ->assertJsonStructure(['jsonrpc', 'error', 'id'])
            ->assertJsonPath('id', $id)
            ->assertJsonPath('error.code', -32601);
    }

    /**
     * @return void
     */
    public function testEmptyRequestPassing(): void
    {
        $this
            ->post('/rpc', [])
            ->assertStatus(200)
            ->assertJsonStructure(['jsonrpc', 'error', 'id'])
            ->assertJsonPath('id', null)
            ->assertJsonPath('error.code', -32700);
    }

    /**
     * @return void
     */
    public function testRpcMethodThrowsAnExceptionProcessing(): void
    {
        $this->router->on($method = 'foo', static function (): void {
            throw new RuntimeException('foo exception');
        });

        $this
            ->postJson('/rpc', [
                'jsonrpc' => 2.0,
                'method'  => $method,
                'id'      => $id = Str::random(),
            ])
            ->assertStatus(200)
            ->assertJsonStructure(['jsonrpc', 'error', 'id'])
            ->assertJsonPath('id', $id)
            ->assertJsonPath('error.code', -32603)
            ->assertJsonPath('error.data.message', 'foo exception');
    }

    /**
     * @return void
     */
    public function testServerErrorProcessing(): void
    {
        $this->app->extend(KernelInterface::class, static function (KernelInterface $original) {
            return m::mock($original)->makePartial()
                ->shouldReceive('handle')
                ->andThrows(new RuntimeException('foo exception'))
                ->getMock();
        });

        $this
            ->postJson('/rpc', [
                'jsonrpc' => 2.0,
                'method'  => Str::random(),
                'id'      => Str::random(),
            ])
            ->assertStatus(200)
            ->assertJsonStructure(['jsonrpc', 'error', 'id'])
            ->assertJsonPath('error.message', 'Server error: foo exception')
            ->assertJsonPath('error.code', -32099);
    }
}
