<?php

namespace AvtoDev\JsonRpc\Responses;

use Countable;
use LogicException;
use IteratorAggregate;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @see ResponsesStack
 *
 * @extends IteratorAggregate<ResponseInterface>
 */
interface ResponsesStackInterface extends Countable, Arrayable, IteratorAggregate
{
    /**
     * Push response into stack.
     *
     * @param ResponseInterface $response
     *
     * @return $this<ResponseInterface>
     */
    public function push($response);

    /**
     * @throws LogicException
     *
     * @return ResponseInterface
     */
    public function first();

    /**
     * @return ResponseInterface[]
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
