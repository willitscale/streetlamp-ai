<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Builders;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Ai\Models\JsonRpc\Error;
use willitscale\Streetlamp\Ai\Models\JsonRpc\Response;
use willitscale\Streetlamp\Builders\ResponseBuilder;

class JsonRpcResponseBuilder extends ResponseBuilder
{
    private string|int $id;
    private object|array|null $result = null;
    private ?Error $error = null;

    public function setId(string|int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setResult(object|array|null $data): self
    {
        $this->result = $data;
        return $this;
    }

    public function setError(?Error $error): self
    {
        $this->error = $error;
        return $this;
    }

    public function build(): ResponseInterface
    {
        $this->setData(
            new Response(
                '2.0',
                $this->id,
                $this->result,
                $this->error
            )
        );

        return parent::build();
    }

    // Maybe call the function from the SSE response builder?
}
