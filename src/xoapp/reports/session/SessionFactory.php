<?php

namespace xoapp\reports\session;

use pocketmine\player\Player;

class SessionFactory
{
    /** @var Session[] */
    private static array $sessions = [];

    public static function create(Player $player): void
    {
        self::$sessions[$player->getName()] = new Session($player->getXuid(), $player->getName());
    }

    public static function get(string $name): ?Session
    {
        return self::$sessions[$name] ?? null;
    }

    public static function delete(string $name): void
    {
        if (!is_null($session = self::get($name))) {
            $session->save();
        }

        unset(self::$sessions[$name]);
    }

    public static function getAll(): array
    {
        return self::$sessions;
    }
}