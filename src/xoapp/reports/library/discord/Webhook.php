<?php

namespace xoapp\reports\library\discord;

use xoapp\reports\library\discord\task\DiscordMessageAsync;
use xoapp\reports\utils\TaskUtils;

final class Webhook
{
    public function __construct(
        protected string $url
    )
    {
    }

    public static function create(string $url): Webhook
    {
        return new Webhook($url);
    }

    public function isValid(): bool
    {
        return filter_var($this->url, FILTER_VALIDATE_URL) !== false;
    }

    public function getURL(): string
    {
        return $this->url;
    }

    public function send(Message $message): void
    {
        TaskUtils::submitAsync(new DiscordMessageAsync($this, $message));
    }
}