<?php

namespace Ustal\StreamHub\Plugins\StreamLifecycle\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandHandlerInterface;
use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;
use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Identifier\IdentifierGeneratorInterface;
use Ustal\StreamHub\Component\Model\StreamParticipant;
use Ustal\StreamHub\Core\Command\ModelCommandBusInterface;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\CreateStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Service\LifecycleSystemEventFactory;

final class StartStreamCommandHandler implements StreamCommandHandlerInterface
{
    private ?IdentifierGeneratorInterface $streamIdGenerator = null;

    public function __construct(
        private readonly ModelCommandBusInterface $modelCommandBus,
        private readonly LifecycleSystemEventFactory $eventFactory,
    ) {}

    public function setStreamIdGenerator(IdentifierGeneratorInterface $streamIdGenerator): void
    {
        $this->streamIdGenerator = $streamIdGenerator;
    }

    public function handle(StreamCommandInterface $command, StreamContextInterface $context): void
    {
        if (!$command instanceof StartStreamCommand) {
            throw new \LogicException('Unexpected command type.');
        }

        $streamId = $this->resolveStreamId($command);
        $participants = $this->buildParticipants($command, $context);

        $this->modelCommandBus->handle(
            new CreateStreamCommand($context, $streamId, $participants),
            $context
        );

        $this->modelCommandBus->handle(
            new AppendStreamEventCommand(
                $context,
                $streamId,
                $this->eventFactory->createStreamStartedEvent($streamId, $participants, $context)
            ),
            $context
        );

        if ($command->firstMessage !== null && trim($command->firstMessage) !== '') {
            $this->modelCommandBus->handle(
                new AppendStreamEventCommand(
                    $context,
                    $streamId,
                    $this->eventFactory->createInitialMessageEvent($streamId, $command->firstMessage, $context)
                ),
                $context
            );
        }
    }

    public static function supports(): string
    {
        return StartStreamCommand::class;
    }

    private function resolveStreamId(StartStreamCommand $command): string
    {
        if ($this->streamIdGenerator !== null) {
            return $this->streamIdGenerator->generate();
        }

        if ($command->contextId !== null && $command->contextId !== '') {
            return $command->contextId;
        }

        throw new \LogicException('StartStreamCommandHandler requires either a configured stream id generator or a non-empty contextId.');
    }

    /**
     * @return StreamParticipant[]
     */
    private function buildParticipants(StartStreamCommand $command, StreamContextInterface $context): array
    {
        $createdAt = new \DateTimeImmutable();
        $currentUserId = $context->getUserId();
        $participantUserIds = [$currentUserId, ...$command->participantUserIds];
        $participantUserIds = array_values(array_unique(array_filter($participantUserIds, static fn (string $userId): bool => $userId !== '')));

        $participants = [];

        foreach ($participantUserIds as $participantUserId) {
            $participants[] = new StreamParticipant(
                userId: $participantUserId,
                displayName: $participantUserId === $currentUserId
                    ? (string) $context->get('display_name', $currentUserId)
                    : null,
                active: true,
                createdAt: $createdAt,
            );
        }

        return $participants;
    }
}
