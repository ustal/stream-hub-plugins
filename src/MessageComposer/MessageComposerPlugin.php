<?php

namespace Ustal\StreamHub\Plugins\MessageComposer;

use Ustal\StreamHub\Component\Plugin\AbstractStreamPlugin;
use Ustal\StreamHub\Component\Plugin\RequiresIdentifierGeneratorsInterface;
use Ustal\StreamHub\Plugins\MessageComposer\Command\SendMessageCommandHandler;
use Ustal\StreamHub\Plugins\MessageComposer\Widget\MessageComposerWidget;

final class MessageComposerPlugin extends AbstractStreamPlugin implements RequiresIdentifierGeneratorsInterface
{
    public const NAME = 'message-composer';

    public static function getCommandHandlers(): array
    {
        return [
            SendMessageCommandHandler::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            MessageComposerWidget::class,
        ];
    }

    public static function getIdentifierGeneratorRequirements(): array
    {
        return ['event_id'];
    }
}
