<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controller;

use willitscale\Streetlamp\Ai\Attributes\McpSession;
use willitscale\Streetlamp\Ai\Attributes\McpStream;
use willitscale\Streetlamp\Ai\Attributes\ModelContextProtocol;
use willitscale\Streetlamp\Ai\Enums\McpVersion;
use willitscale\Streetlamp\Models\JsonRpc\Response;
use willitscale\Streetlamp\Models\ServerSentEvents\Data;
use willitscale\Streetlamp\Models\ServerSentEvents\Event;
use willitscale\Streetlamp\Models\ServerSentEvents\Id;
use willitscale\Streetlamp\Responses\ServerSentEvents;
use willitscale\Streetlamp\Responses\ServerSentEventsDispatcher;

#[ModelContextProtocol('/mcp', McpVersion::LATEST, 'Streetlamp', '1.0.0', 'Client information')]
#[McpStream(McpController::class)]
#[McpSession(McpStream::class)]
class McpController implements ServerSentEventsDispatcher
{
    private int $retries = 10;
    public function dispatch(): array
    {
        return [
            new Id('123'),
            new Event('ping'),
            new Data(new Response('2.0', 'pong', ['timestamp' => time()])),
        ];
    }

    public function isRunning(): bool
    {
        return $this->retries-- > 0;
    }

    public function delay(): void
    {
        sleep(1);
    }
}
