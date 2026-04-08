<?php

namespace Ustal\StreamHub\Plugins\MessageComposer\Widget;

use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Render\RenderResult;
use Ustal\StreamHub\Component\Widget\AbstractStreamWidget;
use Ustal\StreamHub\Plugins\DialogScaffold\Enum\DialogScaffoldSlot;

final class MessageComposerWidget extends AbstractStreamWidget
{
    public const STREAM_ID_CONTEXT_KEY = 'stream_hub.message_composer.stream_id';
    public const ACTION_URL_CONTEXT_KEY = 'stream_hub.message_composer.action_url';
    public const CSRF_INTENTION_CONTEXT_KEY = 'stream_hub.message_composer.csrf_intention';
    public const PLACEHOLDER_CONTEXT_KEY = 'stream_hub.message_composer.placeholder';
    public const SUBMIT_LABEL_CONTEXT_KEY = 'stream_hub.message_composer.submit_label';
    public const MESSAGE_FIELD_NAME_CONTEXT_KEY = 'stream_hub.message_composer.message_field_name';
    public const STREAM_FIELD_NAME_CONTEXT_KEY = 'stream_hub.message_composer.stream_field_name';
    public const CSRF_FIELD_NAME_CONTEXT_KEY = 'stream_hub.message_composer.csrf_field_name';

    public static function getName(): string
    {
        return 'message-composer';
    }

    public static function getSlot(): \BackedEnum
    {
        return DialogScaffoldSlot::BOTTOM;
    }

    public function isVisible(StreamContextInterface $context): bool
    {
        return $context->has(self::STREAM_ID_CONTEXT_KEY)
            && $context->has(self::ACTION_URL_CONTEXT_KEY);
    }

    public function render(StreamContextInterface $context): RenderResult
    {
        return $this->renderTemplate($context, $this->buildTemplateContext($context));
    }

    public static function supports(StreamContextInterface $context): bool
    {
        return true;
    }

    public static function getTemplates(): array
    {
        return [
            'twig' => 'src/MessageComposer/Resources/views/twig/widget/message_composer.html.twig',
        ];
    }

    private function buildTemplateContext(StreamContextInterface $context): array
    {
        $csrfIntention = (string) $context->get(self::CSRF_INTENTION_CONTEXT_KEY, 'stream_hub_send_message');

        return [
            'action_url' => (string) $context->get(self::ACTION_URL_CONTEXT_KEY),
            'stream_id' => (string) $context->get(self::STREAM_ID_CONTEXT_KEY),
            'csrf_token' => $context->getCsrfToken($csrfIntention),
            'csrf_field_name' => (string) $context->get(self::CSRF_FIELD_NAME_CONTEXT_KEY, '_token'),
            'message_field_name' => (string) $context->get(self::MESSAGE_FIELD_NAME_CONTEXT_KEY, 'message'),
            'stream_field_name' => (string) $context->get(self::STREAM_FIELD_NAME_CONTEXT_KEY, 'stream_id'),
            'placeholder' => (string) $context->get(self::PLACEHOLDER_CONTEXT_KEY, 'Write a message'),
            'submit_label' => (string) $context->get(self::SUBMIT_LABEL_CONTEXT_KEY, 'Send'),
        ];
    }
}
