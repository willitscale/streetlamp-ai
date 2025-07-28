<?php

namespace willitscale\Streetlamp\Ai\Controllers;

enum McpVersion: string
{
    case LATEST = self::JUN25->value;
    case JUN25 = '2025-06-18';
    case NOV24 = '2024-11-05';
}
