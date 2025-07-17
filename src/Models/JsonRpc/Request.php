<?php

namespace willitscale\StreetlampAi\Model;

use JsonSerializable;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Request implements JsonSerializable
{
    public function __construct(
        #[JsonProperty] private string $jsonrpc,
        #[JsonProperty] private string|int $id,
        #[JsonProperty] private string $method,
        #[JsonProperty(false)] private ?array $params = null
    ) {
    }

    public function getJsonRpc(): string
    {
        return $this->jsonrpc;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
