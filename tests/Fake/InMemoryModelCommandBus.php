<?php

namespace Ustal\StreamHub\Plugins\Tests\Fake;

use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;
use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Core\Command\ModelCommandBusInterface;

final class InMemoryModelCommandBus implements ModelCommandBusInterface
{
    public ?StreamCommandInterface $lastCommand = null;
    public ?StreamContextInterface $lastContext = null;

    public function handle(StreamCommandInterface $command, StreamContextInterface $context): void
    {
        $this->lastCommand = $command;
        $this->lastContext = $context;
    }
}
