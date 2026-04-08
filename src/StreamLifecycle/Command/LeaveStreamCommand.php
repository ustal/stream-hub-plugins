<?php

namespace Ustal\StreamHub\Plugins\StreamLifecycle\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;

final readonly class LeaveStreamCommand implements StreamCommandInterface
{
    public function __construct(
        public string $streamId,
        public ?string $displayName = null,
    ) {}
}
