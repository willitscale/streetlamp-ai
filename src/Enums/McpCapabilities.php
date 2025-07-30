<?php

namespace willitscale\Streetlamp\Ai\Enums;

enum McpCapabilities: string
{
    case PROMPTS = 'prompts';
    case RESOURCES = 'resources';
    case TOOLS = 'tools';
    case LOGGING = 'logging';
    case COMPLETIONS = 'completions';
    case EXPERIMENTAL = 'experimental';
}
