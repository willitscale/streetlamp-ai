<?php

namespace willitscale\Streetlamp\Ai\Handlers;

interface McpSessionHandler
{
    public function start(?string $sessionId = null): void;
    public function set(string $key, mixed $value): void;
    public function get(string $key): mixed;
    public function delete(string $key): void;
    public function clear(): void;
    public function has(string $key): bool;
    public function getAll(): array;
    public function getSessionId(): string;
}
