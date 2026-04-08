<?php

namespace Ustal\StreamHub\Plugins\MessageComposer\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandHandlerInterface;
use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;
use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Core\Command\ModelCommandBusInterface;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;
use Ustal\StreamHub\Plugins\MessageComposer\Service\MessageEventFactory;

final readonly class SendMessageCommandHandler implements StreamCommandHandlerInterface
{
    public function __construct(
        private ModelCommandBusInterface $modelCommandBus,
        private MessageEventFactory $eventFactory,
    ) {}

    public function handle(StreamCommandInterface $command, StreamContextInterface $context): void
    {
        if (!$command instanceof SendMessageCommand) {
            throw new \LogicException('Unexpected command type.');
        }

        $event = $this->eventFactory->createMessageEvent($command, $context);

        $this->modelCommandBus->handle(
            new AppendStreamEventCommand($context, $command->streamId, $event),
            $context
        );
    }

    public static function supports(): string
    {
        return SendMessageCommand::class;
    }
}
