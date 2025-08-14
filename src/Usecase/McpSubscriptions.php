<?php

namespace willitscale\Streetlamp\Ai\Usecase;

use willitscale\Streetlamp\Ai\Handlers\McpSessionHandler;

readonly class McpSubscriptions
{
    public function __construct(
        private McpSessionHandler $sessionHandler,
    ) {
    }

    public function subscribe(string $topic): void
    {
        $subscriptions = $this->sessionHandler->get('subscriptions') ?? [];
        $subscriptions [] = $topic;
        $this->sessionHandler->set('subscriptions', array_unique($subscriptions));
    }

    public function unsubscribe(string $topic): void
    {
        $subscriptions = array_diff(
            $this->sessionHandler->get('subscriptions') ?? [],
            [$topic]
        );
        $this->sessionHandler->set('subscriptions', array_unique($subscriptions));
    }
}
