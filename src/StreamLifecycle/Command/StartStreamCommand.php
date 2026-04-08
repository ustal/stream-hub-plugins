<?php

namespace Ustal\StreamHub\Plugins\StreamLifecycle\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;
use Ustal\StreamHub\Component\Model\StreamParticipant;

final readonly class StartStreamCommand implements StreamCommandInterface
{
    /**
     * @param StreamParticipant[] $participants
     */
    public function __construct(
        public string $streamId,
        public array $participants,
    ) {}
}
