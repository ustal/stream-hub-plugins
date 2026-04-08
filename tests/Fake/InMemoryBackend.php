<?php

namespace Ustal\StreamHub\Plugins\Tests\Fake;

use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Model\Stream;
use Ustal\StreamHub\Component\Model\StreamCollection;
use Ustal\StreamHub\Component\Model\StreamEvent;
use Ustal\StreamHub\Component\Model\StreamParticipant;
use Ustal\StreamHub\Component\Storage\StreamBackendInterface;

final class InMemoryBackend implements StreamBackendInterface
{
    public ?StreamContextInterface $lastContext = null;
    public ?string $lastStreamId = null;
    public ?StreamEvent $lastEvent = null;

    public function createStream(StreamContextInterface $context, array $participants): Stream
    {
        throw new \BadMethodCallException('Not implemented for this test double.');
    }

    public function joinStream(StreamContextInterface $context, string $streamId, StreamParticipant $participant): Stream
    {
        throw new \BadMethodCallException('Not implemented for this test double.');
    }

    public function getStream(StreamContextInterface $context, string $streamId): ?Stream
    {
        throw new \BadMethodCallException('Not implemented for this test double.');
    }

    public function getStreams(StreamContextInterface $context): StreamCollection
    {
        throw new \BadMethodCallException('Not implemented for this test double.');
    }

    public function appendEvent(StreamContextInterface $context, string $streamId, StreamEvent $event): StreamEvent
    {
        $this->lastContext = $context;
        $this->lastStreamId = $streamId;
        $this->lastEvent = $event;

        return $event;
    }

    public function markRead(StreamContextInterface $context, string $streamId): void
    {
        throw new \BadMethodCallException('Not implemented for this test double.');
    }

    public function getUnreadStreamCount(StreamContextInterface $context): int
    {
        throw new \BadMethodCallException('Not implemented for this test double.');
    }

    public function getUnreadEventCount(StreamContextInterface $context): int
    {
        throw new \BadMethodCallException('Not implemented for this test double.');
    }

    public function getUnreadEventCountForStream(StreamContextInterface $context, string $streamId): int
    {
        throw new \BadMethodCallException('Not implemented for this test double.');
    }
}
