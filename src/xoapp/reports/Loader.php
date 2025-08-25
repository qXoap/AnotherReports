<?php

namespace xoapp\reports;

use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use xoapp\reports\commands\ReportCommand;
use xoapp\reports\session\SessionFactory;

class Loader extends PluginBase
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    protected function onEnable(): void
    {
        self::setInstance($this);

        $this->saveDefaultConfig();
        date_default_timezone_set($this->getConfig()->get("default_timezone", "America/Mexico_City"));

        if (!is_dir($this->getDataFolder() . "players/")) {
            mkdir($this->getDataFolder() . "players/");
        }

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
        $this->getServer()->getCommandMap()->register("report", new ReportCommand($this));
    }

    protected function onDisable(): void
    {
        foreach (SessionFactory::getAll() as $session) {
            $session->save();
        }
    }

    public function getReportReasons(): array
    {
        return $this->getConfig()->get("reasons", []);
    }
}