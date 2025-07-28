<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controllers;

use willitscale\Streetlamp\Ai\Controllers\AgeModel;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonIgnore;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonObject;
use willitscale\Streetlamp\Attributes\DataBindings\Json\JsonProperty;
use willitscale\Streetlamp\Attributes\Validators\AlphabeticValidator;

#[JsonObject]
class TestModel
{
    public function __construct(
        #[JsonProperty(true)] #[AlphabeticValidator] private string $name,
        #[JsonProperty(false)] #[JsonIgnore] private string $email,
        #[JsonProperty] private AgeModel $data
    ) {
    }
}
