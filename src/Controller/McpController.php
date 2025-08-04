<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Controller;

use willitscale\Streetlamp\Ai\Attributes\ModelContextProtocol;
use willitscale\Streetlamp\Ai\Enums\McpVersion;

#[ModelContextProtocol('/mcp', McpVersion::LATEST, 'Streetlamp', '1.0.0', 'Client information')]
class McpController
{
}
