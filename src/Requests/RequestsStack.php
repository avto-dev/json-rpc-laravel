<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Requests;

use Illuminate\Support\Collection;

class RequestsStack extends Collection implements RequestsStackInterface
{
    /**
     * @var bool
     */
    protected $is_batch;

    /**
     * The items contained in the stack.
     *
     * @var ErroredRequestInterface[]|RequestInterface[]
     */
    protected $items = [];

    /**
     * RequestsStack constructor.
     *
     * @param bool                                         $is_batch
     * @param ErroredRequestInterface[]|RequestInterface[] $requests
     */
    public function __construct(bool $is_batch, array $requests = [])
    {
        $this->is_batch = $is_batch;

        parent::__construct($requests);
    }

    /**
     * Push request into stack.
     *
     * @param ErroredRequestInterface|RequestInterface $request
     *
     * @return $this
     */
    public function push($request): self
    {
        if ($request instanceof RequestInterface) {
            $this->items[] = $request;
        } elseif ($request instanceof ErroredRequestInterface) {
            $this->items[] = $request;
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
