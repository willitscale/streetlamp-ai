<?php

namespace willitscale\Streetlamp\Ai\Handlers;

class McpSessionFileHandler implements McpSessionHandler
{
    private string $sessionId;
    private array $data;
    private int $lastModified = 0;
    // TODO: store changes in memory so if the file changes we only apply those changes
    private array $modified = [];
    private array $delete = [];

    public function readIfEmpty(): void
    {
        $file = '/tmp/' . $this->sessionId . '.json';

        if (empty($this->data) || $this->lastModified === filemtime($file)) {
            $this->read($file);
        }
    }

    private function read(string $file): void
    {
        if (!file_exists($file)) {
            return; // TODO: Throw exception here
        }

        $handler = fopen($file, "r+");

        if(flock($handler, LOCK_EX)) {
            $contents = fread($handler, filesize($file));
            flock($handler, LOCK_UN);
        }

        fclose($handler);

        $this->data = json_decode($contents, true) ?? [];
    }

    private function write(): void
    {
        $file = '/tmp/' . $this->sessionId . '.json';
        $handler = fopen($file, "w+");

        if(flock($handler, LOCK_EX)) {
            ftruncate($handler, 0);      // truncate file
            fwrite($handler, json_encode($this->data, JSON_PRETTY_PRINT));
            fflush($handler);
            flock($handler, LOCK_UN);
        }

        fclose($handler);

    }

    public function start(?string $sessionId = null): void
    {
        $this->sessionId = $sessionId ?? $this->getSessionId();
    }

    public function set(string $key, mixed $value): void
    {
        $this->readIfEmpty();
        $this->modified[$key] = $value;
    }

    public function get(string $key): mixed
    {
        $this->readIfEmpty();
        return $this->modified[$key] ?? $this->data[$key] ?? null;
    }

    public function delete(string $key): void
    {
        $this->readIfEmpty();
        $this->delete []= $key;
    }

    public function clear(): void
    {
        $this->readIfEmpty();
        $this->delete = array_keys($this->data);
    }

    public function has(string $key): bool
    {
        $this->readIfEmpty();
        return isset($this->modified[$key]) || isset($this->data[$key]);
    }

    public function getAll(): array
    {
        $this->readIfEmpty();
        return array_merge(
            array_intersect_key($this->data, array_flip($this->delete)),
            $this->modified
        );
    }

    public function getSessionId(): string
    {
        return uniqid('mcp_', true);
    }
}
