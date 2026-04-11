<?php

namespace Ustal\StreamHub\Plugins\StreamLifecycle\Service;

use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Enum\StreamEventType;
use Ustal\StreamHub\Component\Identifier\IdentifierGeneratorInterface;
use Ustal\StreamHub\Component\Model\StreamEvent;
use Ustal\StreamHub\Component\Model\StreamParticipant;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\LeaveStreamCommand;

final readonly class LifecycleSystemEventFactory
{
    public function __construct(private IdentifierGeneratorInterface $eventIdGenerator)
    {
    }

    /**
     * @param StreamParticipant[] $participants
     */
    public function createStreamStartedEvent(string $streamId, array $participants, StreamContextInterface $context): StreamEvent
    {
        return new StreamEvent(
            id: $this->eventIdGenerator->generate(),
            streamId: $streamId,
            userId: $context->getUserId(),
            type: StreamEventType::SYSTEM,
            content: sprintf('%s started the conversation.', $this->resolveInitiatorName($participants, $context)),
            createdAt: new \DateTimeImmutable(),
        );
    }

    public function createInitialMessageEvent(string $streamId, string $content, StreamContextInterface $context): StreamEvent
    {
        return new StreamEvent(
            id: $this->eventIdGenerator->generate(),
            streamId: $streamId,
            userId: $context->getUserId(),
            type: StreamEventType::MESSAGE,
            content: trim($content),
            createdAt: new \DateTimeImmutable(),
        );
    }

    public function createStreamLeftEvent(
        LeaveStreamCommand $command,
        StreamContextInterface $context,
        \DateTimeImmutable $leftAt,
    ): StreamEvent
    {
        return new StreamEvent(
            id: $this->eventIdGenerator->generate(),
            streamId: $command->streamId,
            userId: $context->getUserId(),
            type: StreamEventType::SYSTEM,
            content: sprintf(
                '%s decided to end the dialog.',
                $command->displayName ?? $context->getUserId()
            ),
            createdAt: $leftAt,
        );
    }

    /**
     * @param StreamParticipant[] $participants
     */
    private function resolveInitiatorName(array $participants, StreamContextInterface $context): string
    {
        foreach ($participants as $participant) {
            if ($participant->userId === $context->getUserId()) {
                return $participant->displayName ?? $participant->userId;
            }
        }

        return $context->getUserId();
    }
}
