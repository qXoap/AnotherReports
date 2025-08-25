<?php

namespace xoapp\reports\commands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use xoapp\reports\forms\FormManager;

class AdminSubCommand extends BaseSubCommand
{
    public function __construct(PluginBase $base)
    {
        parent::__construct($base, "admin");
        $this->setPermission("report.admin.command");
    }

    protected function prepare(): void
    {
        // TODO: Implement prepare() method.
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            FormManager::sendPlayerList($sender);
        }
    }
}