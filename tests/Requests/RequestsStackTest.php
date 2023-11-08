<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Requests;

use LogicException;
use Illuminate\Support\Str;
use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Requests\RequestsStack;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\Requests\ErroredRequest;
use AvtoDev\JsonRpc\Errors\MethodNotFoundError;
use AvtoDev\JsonRpc\Requests\RequestsStackInterface;

/**
 * @covers \AvtoDev\JsonRpc\Requests\RequestsStack
 */
class RequestsStackTest extends AbstractTestCase
{
    /**
     * @var RequestsStack
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = $this->instanceFactory();
    }

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
     * @return void
     */
    public function testAll(): void
    {
        $this->instance->push($first = new Request(Str::random(), Str::random()));
        $this->instance->push($second = new Request(Str::random(), Str::random()));

        $this->assertSame([$first, $second], $this->instance->all());
    }

    /**
     * @return void
     */
    public function testGetIterator(): void
    {
        $this->instance->push($first = new Request(Str::random(), Str::random()));
        $this->instance->push($second = new Request(Str::random(), Str::random()));

        $iterator = $this->instance->getIterator();

        $this->assertSame($first, $iterator[0]);
        $this->assertSame($second, $iterator[1]);
    }

    /**
     * @return void
     */
    public function testCount(): void
    {
        $this->assertCount(0, $this->instance);

        $this->instance->push($first = new Request(Str::random(), Str::random()));
        $this->assertCount(1, $this->instance);

        $this->instance->push($second = new Request(Str::random(), Str::random()));
        $this->assertCount(2, $this->instance);
    }

    /**
     * @return void
     */
    public function testFirst(): void
    {
        $this->instance->push($first = new Request(Str::random(), Str::random()));
        $this->instance->push($second = new Request(Str::random(), Str::random()));

        $this->assertSame($first, $this->instance->first());
    }

    /**
     * @return void
     */
    public function testFirstThrownAnExceptionWhenStackInEmpty(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('~is empty~');

        $this->instance->first();
    }

    /**
     * @return RequestsStack
     */
    protected function instanceFactory()
    {
        return new RequestsStack(true);
    }
}
