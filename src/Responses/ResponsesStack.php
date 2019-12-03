<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Responses;

use Illuminate\Support\Collection;

class ResponsesStack extends Collection implements ResponsesStackInterface
{
    /**
     * @var bool
     */
    protected $is_batch;

    /**
     * ResponsesStack constructor.
     *
     * @param bool                $is_batch
     * @param ResponseInterface[] $responses
     */
    public function __construct(bool $is_batch, array $responses = [])
    {
        $this->is_batch = $is_batch;

        parent::__construct($responses);
    }

    /**
     * Push response into stack.
     *
     * @param ResponseInterface $response
     *
     * @return $this
     */
    public function push($response): self
    {
        if ($response instanceof ResponseInterface) {
            $this->items[] = $response;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isBatch(): bool
    {
        return $this->is_batch;
    }
}
