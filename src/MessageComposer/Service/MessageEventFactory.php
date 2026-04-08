<?php

namespace Ustal\StreamHub\Plugins\MessageComposer\Service;

use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Enum\StreamEventType;
use Ustal\StreamHub\Component\Identifier\IdentifierGeneratorInterface;
use Ustal\StreamHub\Component\Model\StreamEvent;
use Ustal\StreamHub\Plugins\MessageComposer\Command\SendMessageCommand;

final readonly class MessageEventFactory
{
    public function __construct(private IdentifierGeneratorInterface $eventIdGenerator)
    {
    }

    public function createMessageEvent(SendMessageCommand $command, StreamContextInterface $context): StreamEvent
    {
        return new StreamEvent(
            id: $this->eventIdGenerator->generate(),
            streamId: $command->streamId,
            userId: $context->getUserId(),
            type: StreamEventType::MESSAGE,
            content: trim($command->content),
            createdAt: new \DateTimeImmutable(),
        );
    }
}
