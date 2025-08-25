<?php

namespace xoapp\reports\library\discord;

use JsonSerializable;

class Message implements JsonSerializable
{
    protected array $data = [];

    public static function create(): Message
    {
        return new Message();
    }

    public function setContent(string $content): Message
    {
        $this->data['content'] = $content;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->data['content'] ?? null;
    }

    public function getUsername(): ?string
    {
        return $this->data['username'];
    }

    public function setUsername(string $username): Message
    {
        $this->data['username'] = $username;
        return $this;
    }

    public function getAvatarURL(): ?string
    {
        return $this->data['avatar_url'] ?? null;
    }

    public function setAvatarURL(string $avatarURL): Message
    {
        $this->data['avatar_url'] = $avatarURL;
        return $this;
    }

    public function addEmbed(Embed $embed): Message
    {
        if (!empty(($arr = $embed->asArray()))) {
            $this->data['embeds'][] = $arr;
        }
        return $this;
    }

    public function setTextToSpeech(bool $ttsEnabled): Message
    {
        $this->data['tts'] = $ttsEnabled;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }

    public function attachFile(string $fileName, ?string $mimeType = null, ?string $postedFileName = null): Message
    {
        $this->data['file'] = curl_file_create($fileName, $mimeType, $postedFileName);
        return $this;
    }

    public function hasFile(): bool
    {
        return isset($this->data['file']);
    }
}