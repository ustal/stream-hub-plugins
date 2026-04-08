<?php

namespace Ustal\StreamHub\Plugins\MessageComposer\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;

final readonly class SendMessageCommand implements StreamCommandInterface
{
    public function __construct(
        public string $streamId,
        public string $content,
    ) {}
}
