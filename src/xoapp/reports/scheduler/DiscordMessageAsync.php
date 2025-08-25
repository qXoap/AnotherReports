<?php

namespace xoapp\reports\library\discord\task;

use xoapp\reports\library\discord\Webhook;
use xoapp\reports\library\discord\Message;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class DiscordMessageAsync extends AsyncTask
{
    private readonly string $url;
    private readonly mixed $message;
    private readonly bool $hasFile;

    public function __construct(
        Webhook $webhook,
        Message $message
    )
    {
        $this->url = $webhook->getURL();
        $this->hasFile = $message->hasFile();

        if ($message->hasFile()) {
            $this->message = $message->jsonSerialize();
        } else {
            $this->message = json_encode($message);
        }
    }

    public function onRun(): void
    {
        $result = $this->send($this->url, $this->message, $this->hasFile);
        $this->setResult($result);
    }

    public function onCompletion(): void
    {
        $response = $this->getResult();

        if (in_array($response[1], [200, 204])) {
            return;
        }
        Server::getInstance()->getLogger()->error('[DiscordWebhookAPI] Got error (' . $response[1] . '): ' . $response[0]);
    }

    private function send(string $url, mixed $message, bool $hasFile): array
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 400);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);

        $contentType = $hasFile ? 'Content-Type: multipart/form-data' : 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$contentType]);

        $ret = [curl_exec($ch), curl_getinfo($ch, CURLINFO_RESPONSE_CODE)];
        curl_close($ch);

        return $ret;
    }
}