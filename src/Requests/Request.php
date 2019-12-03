<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Requests;

use InvalidArgumentException;
use AvtoDev\JsonRpc\Traits\ValidateNonStrictValuesTrait;

class Request implements RequestInterface
{
    use ValidateNonStrictValuesTrait;

    /**
     * @var int|string|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array|object|null
     */
    protected $params;

    /**
     * @var mixed
     */
    protected $raw;

    /**
     * Request constructor.
     *
     * @param int|string|null   $id     Request identifier
     * @param string            $method Requested method name
     * @param array|object|null $params Request parameters
     * @param object|null       $raw    Raw request interpretation
     *
     * @throws InvalidArgumentException If passed not valid arguments
     */
    public function __construct($id, string $method, $params = null, $raw = null)
    {
        $this->validateIdValue($id, true);
        $this->validateParamsValue($params, true);

        $this->id     = $id;
        $this->method = $method;
        $this->params = $params;
        $this->raw    = $raw;
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
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterByPath(string $path, $default = null, string $delimiter = '.')
    {
        return \array_reduce((array) \explode($delimiter, $path), function ($carry, $item) use (&$default) {
            return \is_numeric($item) || \is_array($carry)
                ? ($carry[$item] ?? $default)
                : ($carry->$item ?? $default);
        }, $this->params);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotification(): bool
    {
        return $this->id === null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawRequest()
    {
        return $this->raw;
    }
}
