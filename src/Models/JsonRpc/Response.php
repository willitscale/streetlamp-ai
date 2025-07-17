<?php

namespace willitscale\StreetlampAi\Model;

use JsonSerializable;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Response implements JsonSerializable
{
    public function __construct(
        #[JsonProperty] private string $jsonrpc,
        #[JsonProperty] private string|int $id,
        #[JsonProperty(false)] private ?array $result,
        #[JsonProperty(false)] private ?Error $error,
    ) {
    }

    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
