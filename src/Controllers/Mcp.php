<?php

namespace willitscale\Streetlamp\Ai\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Ai\Models\JsonRpc\Request;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
use willitscale\Streetlamp\Builders\ResponseBuilder;
use willitscale\Streetlamp\Enums\HttpMethod;
use willitscale\Streetlamp\Enums\HttpStatusCode;
use willitscale\Streetlamp\Enums\MediaType;

#[RouteController]
#[Path('/mcp')]
class Mcp
{
    #[Method(HttpMethod::POST)]
    public function post(
        #[BodyParameter] Request $data
    ): ResponseInterface {
        return new ResponseBuilder()
            ->setData($data)
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->build();
    }
}
