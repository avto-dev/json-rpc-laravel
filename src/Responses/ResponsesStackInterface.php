<?php

namespace AvtoDev\JsonRpc\Responses;

use Countable;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;
use LogicException;

/**
 * @see ResponsesStack
 */
interface ResponsesStackInterface extends Countable, Arrayable, IteratorAggregate
{
    /**
     * Push response into stack.
     *
     * @param ResponseInterface $response
     *
     * @return $this
     */
    public function push($response);

    /**
     * @throws LogicException
     *
     * @return ResponseInterface
     */
    public function first();

    /**
     * @return array|ResponseInterface[]
     */
    public function all();

    /**
     * Is batch response?
     *
     * @return bool
     */
    public function isBatch(): bool;

    /**
     * Determine if stack is not empty.
     *
     * @return bool
     */
    public function isNotEmpty();
}
