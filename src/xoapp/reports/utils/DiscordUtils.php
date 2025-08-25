<?php

namespace xoapp\reports\utils;

use xoapp\reports\library\discord\Embed;
use xoapp\reports\library\discord\Message;
use xoapp\reports\library\discord\Webhook;
use xoapp\reports\Loader;
use xoapp\reports\object\Report;
use xoapp\reports\session\Session;

class DiscordUtils
{
    public static function sendReportMessage(Session $session, Report $report): void
    {
        $webhookURL = Loader::getInstance()->getConfig()->get("discord_url");
        if (empty($webhookURL)) {
            return;
        }

        $webhook = new Webhook($webhookURL);
        $message = new Message();
        $embed = new Embed();

        $embed->addField("ID", $report->getId());
        $embed->addField("Sender", $report->getSender());
        $embed->addField("Reason", $report->getReason());
        $embed->addField("Date Time", $report->getDateTime());
        $embed->addField("Description", $report->getDescription());

        $message->addEmbed($embed);
        $webhook->send($message);
    }
}