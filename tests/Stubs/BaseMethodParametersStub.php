<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Stubs;

use AvtoDev\JsonRpc\MethodParameters\BaseMethodParameters;

class BaseMethodParametersStub extends BaseMethodParameters
{
    /**
     * @var string|null
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function parse($params): void
    {
        if ($this->validateParamsValue($params)) {
            $this->extractIdParameter($params);
        }

        parent::parse($params);
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $params
     */
    protected function extractIdParameter($params): void
    {
        if (\is_object($params)) {
            $params = \get_object_vars($params);
        }

        if (\is_array($params) && \is_string($id = $params['id'] ?? null)) {
            $this->id = $id;
        }
    }
}
