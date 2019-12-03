<?php

namespace AvtoDev\JsonRpc\Tests\Unit;

use AvtoDev\JsonRpc\Tests\AbstractTestCase;

abstract class AbstractUnitTestCase extends AbstractTestCase
{
    /**
     * Mock some property for a object.
     *
     * @param object $object
     * @param string $property_name
     * @param mixed  $value
     *
     * @return void
     */
    protected function mockProperty($object, string $property_name, $value): void
    {
        $reflection = new \ReflectionClass($object);

        $property = $reflection->getProperty($property_name);

        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }

    /**
     * Asserts that a variable is of a given types.
     *
     * @param array  $expected
     * @param        $actual
     * @param string $message
     *
     * @return void
     */
    protected function assertInstanceOfMultiple(array $expected, $actual, string $message = ''): void
    {
        foreach ($expected as $item) {
            $this->assertInstanceOf($item, $actual, $message);
        }
    }
}
