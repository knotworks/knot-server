<?php

declare(strict_types=1);

namespace NotificationChannels\Telegram;

class TelegramMessage
{
    public function __construct(public ?string $content = null) {}

    public static function create(): self
    {
        return new self;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->content,
            'parse_mode' => 'Markdown',
        ];
    }
}
