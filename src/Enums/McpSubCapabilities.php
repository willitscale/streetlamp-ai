<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Enums;

enum McpSubCapabilities: string
{
    case LIST_CHANGED = 'listChanged';
    case SUBSCRIBE = 'subscribe';
}
