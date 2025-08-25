<?php

namespace xoapp\reports\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use xoapp\reports\forms\FormManager;
use xoapp\reports\Loader;
use xoapp\reports\session\SessionFactory;

class ReportCommand extends BaseCommand
{
    public function __construct(private readonly PluginBase $base)
    {
        parent::__construct($this->base, "report", "Report a Player");
        $this->setPermission("report.command");
    }

    protected function prepare(): void
    {
        $this->registerSubCommand(new AdminSubCommand($this->base));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $sessions = SessionFactory::getAll();
        $reasons = Loader::getInstance()->getReportReasons();

        if (count($sessions) <= 1) {
            $sender->sendMessage(TextFormat::colorize("&cNo Players online"));
            return;
        }

        if (count($reasons) <= 1) {
            $sender->sendMessage(TextFormat::colorize("&cReport config error, check config.yml"));
            return;
        }


        FormManager::sendMakeReport($sender);
    }
}