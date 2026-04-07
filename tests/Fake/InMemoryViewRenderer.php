<?php

namespace Ustal\StreamHub\Plugins\Tests\Fake;

use Ustal\StreamHub\Component\Render\ViewRendererInterface;

final class InMemoryViewRenderer implements ViewRendererInterface
{
    public ?string $lastTemplate = null;
    public ?array $lastContext = null;

    public function __construct(
        private readonly string $name = 'twig',
        private readonly string $html = '<div>layout</div>',
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function render(string $template, array $context = []): string
    {
        $this->lastTemplate = $template;
        $this->lastContext = $context;

        return $this->html;
    }
}
