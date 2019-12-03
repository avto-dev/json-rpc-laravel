<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Requests;

use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Requests\RequestInterface;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;
use Illuminate\Support\Str;
use InvalidArgumentException;
use stdClass;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Requests\Request<extended>
 */
class RequestTest extends AbstractUnitTestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new Request(Str::random(), Str::random(), []);
    }

    /**
     * @coversNothing
     *
     * @small
     *
     * @return void
     */
    public function testInterfaces(): void
    {
        $this->assertInstanceOf(RequestInterface::class, $this->request);
    }

    /**
     * @small
     *
     * @return void
     */
    public function testIdGetter(): void
    {
        foreach ([Str::random(), null, \random_int(1, 999)] as $id) {
            $this->assertSame($id, (new Request($id, Str::random()))->getId());
        }
    }

    /**
     * @small
     *
     * @return void
     */
    public function testGetters(): void
    {
        $request = new Request(
            $id = Str::random(),
            $method = Str::random(),
            $params = [1, Str::random(), null],
            $raw = new stdClass
        );

        $this->assertSame($raw, $request->getRawRequest());
        $this->assertSame($id, $request->getId());
        $this->assertSame($method, $request->getMethod());
        $this->assertSame($params, $request->getParams());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testisNotification(): void
    {
        $this->assertTrue((new Request(null, Str::random()))->isNotification());
        $this->assertFalse((new Request(Str::random(), Str::random()))->isNotification());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testInvalidIdPassingIntoConstructor(): void
    {
        foreach ([M_PI, [], new \stdClass, \tmpfile(), true] as $wrong_id) {
            $catch = false;

            try {
                new Request($wrong_id, Str::random());
            } catch (InvalidArgumentException $e) {
                $catch = true;
            }

            $this->assertTrue($catch);
        }
    }

    /**
     * @small
     *
     * @return void
     */
    public function testGetParameterByPath(): void
    {
        $this->request = new Request(Str::random(), Str::random(), $params = [
            'foo' => $foo = Str::random(),
            'bar' => $bar = \random_int(1, 100),
            'baz' => [
                'foo' => $baz_foo = [],
                'bar' => $baz_bar = (object) [
                    'foo' => $baz_bar_foo = Str::random(),
                    'bar' => [
                        'foo' => $baz_bar_bar_foo = [],
                        'bar' => [$baz_bar_bar_bar_0 = \random_int(1, 100), $baz_bar_bar_bar_1 = Str::random()],
                    ],
                ],
            ],
        ]);

        $this->assertSame($foo, $this->request->getParameterByPath('foo'));
        $this->assertSame($bar, $this->request->getParameterByPath('bar'));
        $this->assertSame($baz_foo, $this->request->getParameterByPath('baz.foo'));
        $this->assertSame($baz_bar, $this->request->getParameterByPath('baz.bar'));
        $this->assertSame($baz_bar_foo, $this->request->getParameterByPath('baz.bar.foo'));
        $this->assertSame($baz_bar_bar_foo, $this->request->getParameterByPath('baz.bar.bar.foo'));
        $this->assertSame($baz_bar_bar_bar_0, $this->request->getParameterByPath('baz.bar.bar.bar.0'));
        $this->assertSame($baz_bar_bar_bar_1, $this->request->getParameterByPath('baz.bar.bar.bar.1'));

        $this->assertSame($params, $this->request->getParams());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testGetParameterByPathUsingObject(): void
    {
        $this->request = new Request(Str::random(), Str::random(), $params = (object) [
            'foo' => $foo = Str::random(),
            'bar' => $bar = \random_int(1, 100),
            'baz' => (object) [
                'foo' => $baz_foo = (object) [],
                'bar' => $baz_bar = [
                    'foo' => $baz_bar_foo = Str::random(),
                    'bar' => (object) [
                        'foo' => $baz_bar_bar_foo = [],
                        'bar' => [$baz_bar_bar_bar_0 = \random_int(1, 100), $baz_bar_bar_bar_1 = Str::random()],
                    ],
                ],
            ],
        ]);

        $this->assertSame($foo, $this->request->getParameterByPath('foo'));
        $this->assertSame($bar, $this->request->getParameterByPath('bar'));
        $this->assertSame($baz_foo, $this->request->getParameterByPath('baz.foo'));
        $this->assertSame($baz_bar, $this->request->getParameterByPath('baz.bar'));
        $this->assertSame($baz_bar_foo, $this->request->getParameterByPath('baz.bar.foo'));
        $this->assertSame($baz_bar_bar_foo, $this->request->getParameterByPath('baz.bar.bar.foo'));
        $this->assertSame($baz_bar_bar_bar_0, $this->request->getParameterByPath('baz.bar.bar.bar.0'));
        $this->assertSame($baz_bar_bar_bar_1, $this->request->getParameterByPath('baz.bar.bar.bar.1'));

        $this->assertSame($params, $this->request->getParams());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testGetParameterByPathUsingCustomDelimiter(): void
    {
        $this->request = new Request(Str::random(), Str::random(), $params = [
            'foo' => $foo = Str::random(),
            'bar' => $bar = \random_int(1, 100),
            'baz' => [
                'foo' => $baz_foo = [],
                'bar' => $baz_bar = (object) [
                    'foo' => $baz_bar_foo = Str::random(),
                    'bar' => [
                        'foo' => $baz_bar_bar_foo = [],
                    ],
                ],
            ],
        ]);

        $this->assertSame($foo, $this->request->getParameterByPath('foo', null, $delimiter = '*'));
        $this->assertSame($bar, $this->request->getParameterByPath('bar', null, $delimiter));
        $this->assertSame($baz_foo, $this->request->getParameterByPath('baz*foo', null, $delimiter));
        $this->assertSame($baz_bar, $this->request->getParameterByPath('baz^bar', null, '^'));
        $this->assertSame($baz_bar_foo, $this->request->getParameterByPath('baz%bar%foo', null, '%'));
        $this->assertSame($baz_bar_bar_foo, $this->request->getParameterByPath('baz#bar#bar#foo', null, '#'));

        $this->assertSame($params, $this->request->getParams());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testGetParameterByPathDefault(): void
    {
        $this->request = new Request(Str::random(), Str::random(), (object) [
            'foo' => $foo = Str::random(),
            'bar' => $bar = \random_int(1, 100),
        ]);

        $this->assertSame($default = Str::random(), $this->request->getParameterByPath(Str::random(), $default));
        $this->assertSame($bar, $this->request->getParameterByPath('bar'));
        $this->assertSame($default = [], $this->request->getParameterByPath(Str::random(), $default));
        $this->assertSame($default = function (): void {
            //
        }, $this->request->getParameterByPath(Str::random(), $default));
    }
}
