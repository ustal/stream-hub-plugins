<?php

namespace Ustal\StreamHub\Plugins\Tests\Fake;

use Ustal\StreamHub\Component\Context\StreamContextInterface;

final class InMemoryContext implements StreamContextInterface
{
    public function __construct(
        private readonly string $userId = 'user-1',
        private readonly array $values = [],
    ) {}

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getActor(): ?string
    {
        return null;
    }

    public function generateUrl(string $name, array $parameters = []): string
    {
        return '/' . ltrim($name, '/');
    }

    public function getCsrfToken(string $intention): ?string
    {
        return 'csrf-' . $intention;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }
}
