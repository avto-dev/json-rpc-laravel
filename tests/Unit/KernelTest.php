<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Unit;

use stdClass;
use AvtoDev\JsonRpc\Kernel;
use Illuminate\Support\Str;
use AvtoDev\JsonRpc\KernelInterface;
use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Errors\InternalError;
use AvtoDev\JsonRpc\Requests\RequestsStack;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Requests\ErroredRequest;
use AvtoDev\JsonRpc\Responses\ErrorResponse;
use AvtoDev\JsonRpc\Responses\SuccessResponse;
use AvtoDev\JsonRpc\Errors\MethodNotFoundError;
use AvtoDev\JsonRpc\Tests\Stubs\BaseMethodParametersStub;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Kernel<extended>
 */
class KernelTest extends AbstractUnitTestCase
{
    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->kernel = $this->app->make(Kernel::class);
        $this->router = $this->app->make(RouterInterface::class);
    }

    /**
     * @small
     *
     * @return void
     */
    public function testInterfaces(): void
    {
        $this->assertInstanceOf(KernelInterface::class, $this->kernel);
    }

    /**
     * @small
     *
     * @return void
     */
    public function testHandleWithEmpty(): void
    {
        $this->assertEmpty($responses = $this->kernel->handle(new RequestsStack(true)));
        $this->assertTrue($responses->isBatch());

        $this->assertEmpty($responses = $this->kernel->handle(new RequestsStack(false)));
        $this->assertFalse($responses->isBatch());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testHandleNotifications(): void
    {
        $this->router->on($method1 = 'foo', function (): bool {
            return true;
        });

        $this->router->on($method2 = 'bar', function (): bool {
            return false;
        });

        $requests = new RequestsStack(true);
        $requests->push(new Request($id = Str::random(), $method1, null, new stdClass));
        $requests->push(new Request(null, $method2, null, new stdClass)); // Should not returns

        $responses = $this->kernel->handle($requests);

        $this->assertCount(1, $responses);
        $this->assertSame($id, $responses->first()->getId());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testHandleErroredRequests(): void
    {
        $this->router->on($method = 'foo', function (): bool {
            return true;
        });

        $requests = new RequestsStack(true);
        $requests->push(new ErroredRequest(new MethodNotFoundError, $id1 = Str::random()));
        $requests->push(new Request($id2 = Str::random(), $method, null, new stdClass));

        $responses = $this->kernel->handle($requests);

        $this->assertCount(2, $responses);

        /** @var ErrorResponse $first */
        $first = $responses->all()[0];
        $this->assertInstanceOf(ErrorResponse::class, $first);
        $this->assertSame($id1, $first->getId());

        /** @var SuccessResponse $second */
        $second = $responses->all()[1];
        $this->assertSame($id2, $second->getId());
        $this->assertInstanceOf(SuccessResponse::class, $second);
        $this->assertTrue($second->getResult());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testHandleNonExistsMethod(): void
    {
        $requests = new RequestsStack(true);
        $requests->push(new Request($id = Str::random(), Str::random(), null, new stdClass));

        $responses = $this->kernel->handle($requests);

        $this->assertCount(1, $responses);

        /** @var ErrorResponse $first */
        $first = $responses->all()[0];
        $this->assertInstanceOf(ErrorResponse::class, $first);
        $this->assertInstanceOf(MethodNotFoundError::class, $first->getError());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testHandleMethodThrowAnException(): void
    {
        $this->router->on($method = 'foo', function (): void {
            throw new \RuntimeException;
        });

        $requests = new RequestsStack(false);
        $requests->push(new Request($id = Str::random(), $method, null, new stdClass));

        $responses = $this->kernel->handle($requests);

        $this->assertCount(1, $responses);

        /** @var ErrorResponse $first */
        $first = $responses->all()[0];
        $this->assertInstanceOf(ErrorResponse::class, $first);
        $this->assertInstanceOf(InternalError::class, $first->getError());
    }

    /**
     * Test batch call with general request.
     *
     * @medium
     */
    public function testBatchCall(): void
    {
        $this->router->on($name = 'foo', function (BaseMethodParametersStub $parameters): ?string {
            return $parameters->getId();
        });

        $requests_stack = new RequestsStack(true);

        $requests_stack->push(
            new Request($first_request_id = Str::random(), $name, ['id' => $send_id = Str::random()])
        );

        $requests_stack->push(new Request(Str::random(), $name, []));

        $responses = $this->kernel->handle($requests_stack);
        $this->assertTrue($responses->isBatch());
        /** @var SuccessResponse $first */
        $first = $responses->all()[0];
        $this->assertInstanceOf(SuccessResponse::class, $first);
        $this->assertSame($first_request_id, $first->getId());
        $this->assertSame($send_id, $first->getResult());

        /** @var SuccessResponse $last */
        $last = $responses->all()[1];
        $this->assertInstanceOf(SuccessResponse::class, $last);
        $this->assertNull($last->getResult());
    }
}
