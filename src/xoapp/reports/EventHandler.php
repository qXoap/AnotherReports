<?php

namespace xoapp\reports;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use xoapp\reports\session\SessionFactory;

class EventHandler implements Listener
{
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $session = SessionFactory::get($player->getName());

        if ($session === null) {
            SessionFactory::create($player);
        }
    }

    public function onPlayerKick(PlayerKickEvent $event): void
    {
        $player = $event->getPlayer();
        $session = SessionFactory::get($player->getName());

        if ($session === null) {
            return;
        }

        $session->save();;
        SessionFactory::delete($player->getName());
    }
}