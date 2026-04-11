<?php

namespace Ustal\StreamHub\Plugins\StreamLifecycle\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;

final readonly class StartStreamCommand implements StreamCommandInterface
{
    /**
     * @param string[] $participantUserIds
     */
    public function __construct(
        public array $participantUserIds = [],
        public ?string $contextId = null,
        public ?string $firstMessage = null,
    ) {}
}
