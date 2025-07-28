<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controllers;

use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;

#[JsonObject]
class AgeModel
{
    public function __construct(
        #[JsonProperty(true)]
        private int $age = 0
    ) {
    }
}
