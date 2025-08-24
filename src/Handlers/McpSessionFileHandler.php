<?php

namespace willitscale\Streetlamp\Ai\Handlers;

class McpSessionFileHandler implements McpSessionHandler
{
    private bool $started = false;
    private string $sessionId;
    private array $data;
    private int $lastModified = 0;
    private array $modified = [];
    private array $delete = [];

    private function readIfEmpty(): void
    {
        if (!$this->started) {
            $this->start();
        }

        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->sessionId . '.json';
        if (empty($this->data) || $this->lastModified < filemtime($file)) {
            $this->read($file);
        }
    }

    private function read(string $file): void
    {
        if (!file_exists($file) || filesize($file) === 0) {
            file_put_contents($file, json_encode((object)null, JSON_PRETTY_PRINT));
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
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->sessionId . '.json';
        $handler = fopen($file, "rw+");

        if(flock($handler, LOCK_EX)) {
            $contents = fread($handler, filesize($file));
            $data = json_decode(trim($contents), true) ?? [];

            $data = array_filter(
                $data,
                fn($i) => !isset($this->delete[$i]),
                ARRAY_FILTER_USE_KEY
            );

            $data = array_merge(
                $data,
                $this->modified
            );

            ftruncate($handler, 0);
            fwrite($handler, json_encode($data, JSON_PRETTY_PRINT));
            fflush($handler);
            flock($handler, LOCK_UN);
        }

        fclose($handler);
    }

    public function start(?string $sessionId = null): void
    {
        $this->sessionId = (isset($sessionId) && preg_match('/^[a-z0-9\._]+$/i', $sessionId)) ?
            $sessionId : uniqid('mcp_', true);
        $this->started = true;
    }

    public function set(string $key, mixed $value): void
    {
        $this->readIfEmpty();
        $this->modified[$key] = $value;
        $this->write();
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
        unset($this->modified[$key]);
        $this->write();
    }

    public function clear(): void
    {
        $this->readIfEmpty();
        $this->delete = array_keys($this->data);
        $this->modified = [];
        $this->write();
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
        $this->readIfEmpty();
        return $this->sessionId;
    }
}
