<?php

namespace willitscale\Streetlamp\Ai\Models\JsonRpc;

use JsonSerializable;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
readonly class Notification implements JsonSerializable
{
    public function __construct(
        #[JsonProperty] private string $jsonrpc,
        #[JsonProperty] private string $method,
        #[JsonProperty(false)] private ?array $params,
    ) {}

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
