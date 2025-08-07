<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Ai\Capabilities;

use willitscale\Streetlamp\Ai\Attributes\McpCapability;
use willitscale\Streetlamp\Ai\Attributes\McpAction;
use willitscale\Streetlamp\Ai\Attributes\McpSubCapability;
use willitscale\Streetlamp\Ai\Enums\McpCapabilities;
use willitscale\Streetlamp\Ai\Enums\McpSubCapabilities;
use willitscale\Streetlamp\Models\JsonRpc\Request;

#[McpCapability(McpCapabilities::PROMPTS)]
class PromptsCapability
{
    #[McpSubCapability(McpSubCapabilities::SUBSCRIBE)]
    public function subscribe(): mixed
    {
        return null;
    }

    #[McpAction('list')]
    public function listPrompts(Request $request): array
    {
        $cursor = $request->getParams()->cursor ?? null;

        return [
            "prompts" => [
                [
                    "name" => "code_review",
                    "title" => "Request Code Review",
                    "description" => "Asks the LLM to analyze code quality and suggest improvements",
                    "arguments" => [
                        [
                            "name" => "code",
                            "description" => "The code to review",
                            "required" => true
                        ]
                    ]
                ]
            ],
            "nextCursor" => "next-page-cursor"
        ];
    }

    #[McpAction('get')]
    public function getPrompt(Request $request): array
    {
        return [
            "description" => "Code review prompt",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        "type" => "text",
                        "text" => "Please review this Python code: " . $request->getParams()->arguments->code
                    ]
                ]
            ]
        ];
    }
}
