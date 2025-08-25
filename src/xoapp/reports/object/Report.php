<?php

namespace xoapp\reports\object;

use xoapp\reports\session\Session;

readonly class Report
{
    public function __construct(
        private string $id,
        private string $reason,
        private string $sender,
        private string $dateTime,
        private string $description
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function getDateTime(): string
    {
        return $this->dateTime;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'sender' => $this->sender,
            'dateTime' => $this->dateTime,
            'description' => $this->description
        ];
    }

    public static function randomID(Session $session): string
    {
        do {
            $randomID = bin2hex(random_bytes(2));
        } while (isset($session->getReports()[$randomID]));

        return strtoupper($randomID);
    }
}