<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controller;

use willitscale\Streetlamp\Ai\Attributes\McpSession;
use willitscale\Streetlamp\Ai\Attributes\McpStream;
use willitscale\Streetlamp\Ai\Attributes\Mcp;
use willitscale\Streetlamp\Ai\Enums\McpVersion;
use willitscale\Streetlamp\Ai\Handlers\McpSessionFileHandler;
use willitscale\Streetlamp\Models\JsonRpc\Response;
use willitscale\Streetlamp\Models\ServerSentEvents\Data;
use willitscale\Streetlamp\Models\ServerSentEvents\Event;
use willitscale\Streetlamp\Models\ServerSentEvents\Id;
use willitscale\Streetlamp\Responses\ServerSentEventsDispatcher;

#[Mcp('/mcp', McpVersion::LATEST, 'Streetlamp', '1.0.0', 'Client information')]
#[McpStream(McpController::class)]
#[McpSession(McpSessionFileHandler::class)]
class McpController implements ServerSentEventsDispatcher
{
    public function __construct(
        private string $mcpProtocolVersion,
        private string $mcpSessionId,
        private int $retries = 10
    ) {
    }

    public function dispatch(): array
    {
        return [
            new Id('123'),
            new Event('ping'),
            new Data(new Response('2.0', 'pong', [])),
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
