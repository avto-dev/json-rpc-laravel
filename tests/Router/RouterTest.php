<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Router;

use Mockery as m;
use Illuminate\Support\Str;
use InvalidArgumentException;
use AvtoDev\JsonRpc\Router\Router;
use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use Illuminate\Contracts\Foundation\Application;
use AvtoDev\JsonRpc\MethodParameters\BaseMethodParameters;
use AvtoDev\JsonRpc\MethodParameters\MethodParametersInterface;

/**
 * @group  router
 * @covers \AvtoDev\JsonRpc\Router\Router<extended>
 */
class RouterTest extends AbstractTestCase
{
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

        $this->router = $this->app->make(Router::class);
    }

    /**
     * @return void
     */
    public function testInterfaces(): void
    {
        $this->assertInstanceOf(RouterInterface::class, $this->router);
    }

    /**
     * @return void
     */
    public function testActionRegister(): void
    {
        $actions = [
            'foo'  => function (): bool {
                return true;
            },
            'bar'  => static::class . '@someAction',
            'baz'  => \Closure::fromCallable(function (): int {
                return 123;
            }),
            'blah' => [$this, 'someAction'],
        ];

        foreach ($actions as $name => $action) {
            $this->assertFalse($this->router->methodExists($name));

            $this->router->on($name, $action);

            $this->assertTrue($this->router->methodExists($name));
        }

        foreach ($actions as $name => $action) {
            $this->assertContains($name, $this->router->methods());
        }

        $this->assertCount(\count($actions), $this->router->methods());
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testActionRegisterFails(): void
    {
        foreach ([[], null, new \stdClass, \tmpfile(), M_PI, \random_int(1, 999)] as $wrong_action) {
            $catch = false;

            try {
                $this->router->on(Str::random(), $wrong_action);
            } catch (InvalidArgumentException $e) {
                $catch = true;
            }

            $this->assertTrue($catch);
        }
    }

    /**
     * @return void
     */
    public function testHandle(): void
    {
        $actions = [
            'foo'  => static function (): bool {
                return true;
            },
            'bar'  => static::class . '@someAction',
            'baz'  => \Closure::fromCallable(function (): int {
                return 123;
            }),
            'blah' => [$this, 'someAction'],
        ];

        foreach ($actions as $name => $action) {
            $this->router->on($name, $action);
        }

        $this->assertTrue($this->router->call('foo'));
        $this->assertInstanceOf(Application::class, $this->router->call('bar'));
        $this->assertSame(123, $this->router->call('baz'));
        $this->assertInstanceOf(Application::class, $this->router->call('blah'));
    }

    /**
     * @return void
     */
    public function testHandleWithPassingRequestInstance(): void
    {
        $executed = false;

        $request = new Request($id = Str::random(), $method = 'foo', $params = [
            'foo' => 'bar',
        ]);

        $this->app->instance(
            MethodParametersInterface::class,
            m::mock(MethodParametersInterface::class)
                ->shouldReceive('getParams')
                ->andReturnUsing(function () use ($params) {
                    return $params;
                })
                ->getMock()
        );

        $this->router->on($method,
            $action = function (BaseMethodParameters $parameters) use (&$executed, $request): bool {
                $executed = true;

                $this->assertSame($request->getParams(), $parameters->getParams());

                return true;
            });

        $this->assertTrue($this->router->call($method));
        $this->assertTrue($executed);
    }

    /**
     * @return void
     */
    public function testHandleFails(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->router->call('foo');
    }

    /**
     * Required for actions testing.
     *
     * @param Application $app
     *
     * @return Application
     */
    public function someAction(Application $app): Application
    {
        return $app;
    }
}
