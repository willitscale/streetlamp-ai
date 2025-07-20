<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controllers;

use Psr\Http\Message\ResponseInterface;
use willitscale\Streetlamp\Ai\Builders\JsonRpcResponseBuilder;
use willitscale\Streetlamp\Ai\Models\JsonRpc\Request;
use willitscale\Streetlamp\Attributes\Controller\RouteController;
use willitscale\Streetlamp\Attributes\Parameter\BodyParameter;
use willitscale\Streetlamp\Attributes\Path;
use willitscale\Streetlamp\Attributes\Route\Method;
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

        return new JsonRpcResponseBuilder()
            ->setResult(
                [
                    'accepted' => $data->getParams()
                ]
            )
            ->setId($data->getId())
            ->setHttpStatusCode(HttpStatusCode::HTTP_OK)
            ->setContentType(MediaType::APPLICATION_JSON)
            ->build();
    }
}
