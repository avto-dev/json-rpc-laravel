<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Requests;

use Illuminate\Support\Str;
use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Requests\RequestsStack;
use AvtoDev\JsonRpc\Requests\ErroredRequest;
use AvtoDev\JsonRpc\Errors\MethodNotFoundError;
use AvtoDev\JsonRpc\Requests\RequestsStackInterface;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;

/**
 * @covers \AvtoDev\JsonRpc\Requests\RequestsStack<extended>
 */
class RequestsStackTest extends AbstractUnitTestCase
{
    /**
     * @var RequestsStack
     */
    protected $instance;

    /**
     * @return void
     */
    public function testInterface(): void
    {
        $this->assertInstanceOf(RequestsStackInterface::class, $this->instance);
    }

    /**
     * @return void
     */
    public function testMakeMethod(): void
    {
        $this->assertEquals(
            $this->instanceFactory(), $this->instance::make(true)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testStackAccessors(): void
    {
        $this->assertCount(0, $this->instance);

        $this->instance->push($value = new Request(Str::random(), Str::random()));
        $this->instance->push($value2 = new ErroredRequest(new MethodNotFoundError, Str::random()));
        $this->instance->push('foo');
        $this->instance->push([]);

        $this->assertEquals([$value, $value2], $this->instance->all());
        $this->assertCount(2, $this->instance);

        $this->instance->forget([0, 1]);

        $this->assertCount(0, $this->instance);
        $this->assertEquals([], $this->instance->all());
    }

    /**
     * @return void
     */
    public function testEmptyAndNot(): void
    {
        $this->assertTrue($this->instance->isEmpty());
        $this->assertFalse($this->instance->isNotEmpty());

        $this->instance->push($item = new Request(Str::random(), Str::random()));

        $this->assertFalse($this->instance->isEmpty());
        $this->assertTrue($this->instance->isNotEmpty());

        $this->assertSame($item, $this->instance->first());
    }

    /**
     * @return void
     */
    public function testIsBatch(): void
    {
        $this->assertTrue((new RequestsStack(true))->isBatch());
        $this->assertFalse((new RequestsStack(false))->isBatch());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = $this->instanceFactory();
    }

    /**
     * {@inheritdoc}
     *
     * @return RequestsStack
     */
    protected function instanceFactory()
    {
        return new RequestsStack(true);
    }
}
