<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Responses;

use InvalidArgumentException;
use AvtoDev\JsonRpc\Traits\ValidateNonStrictValuesTrait;

class SuccessResponse implements SuccessResponseInterface
{
    use ValidateNonStrictValuesTrait;

    /**
     * Response identifier.
     *
     * @var int|string|null
     */
    protected $id;

    /**
     * Response result data.
     *
     * @var array|bool|float|int|object|string
     */
    protected $result;

    /**
     * SuccessResponse constructor.
     *
     * @param int|string|null                               $id     Response identifier
     * @param array|bool|float|int|mixed|object|string|null $result Response result data
     *
     * @throws InvalidArgumentException If passed not valid arguments
     */
    public function __construct($id, $result)
    {
        $this->validateIdValue($id, true);
        $this->validateResultValue($result, true);

        $this->id     = $id;
        $this->result = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->result;
    }
}
