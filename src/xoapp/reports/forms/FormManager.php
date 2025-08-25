<?php

namespace xoapp\reports\forms;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xoapp\reports\Loader;
use xoapp\reports\object\Report;
use xoapp\reports\session\Session;
use xoapp\reports\session\SessionFactory;
use xoapp\reports\utils\DiscordUtils;

class FormManager
{
    public static function sendPlayerList(Player $player): void
    {
        $buttons = [];
        $sessions = SessionFactory::getAll();

        foreach ($sessions as $session) {
            if ($session->getName() !== $player->getName()) {
                $buttons[] = new MenuOption($session->getName(), new FormIcon("textures/ui/icon_steve", "path"));
            }
        }

        $form = new MenuForm(
            "Sessions List", "", $buttons,
            function (Player $player, int $option) use ($sessions): void
            {
                $session = array_values($sessions)[$option] ?? null;

                if ($session === null) {
                    $player->sendMessage(TextFormat::colorize("&cInvalid Session"));
                    return;
                }

                if (empty($session->getReports())) {
                    $player->sendMessage(TextFormat::colorize("&cThis player has no reports!"));
                    return;
                }

                self::sendPlayerStory($player, $session);
            }
        );

        $player->sendForm($form);
    }

    private static function sendPlayerStory(Player $player, Session $session): void
    {
        $buttons = [];
        foreach ($session->getReports() as $id => $report) {
            $buttons[] = new MenuOption(TextFormat::colorize("ReportID : &3" . $id));
        }

        $form = new MenuForm(
            $session->getName() . " Reports", "", $buttons,
            function (Player $player, int $option) use ($session): void
            {
                $report = array_values($session->getReports())[$option] ?? null;

                if ($report === null) {
                    $player->sendMessage(TextFormat::colorize("&cInvalid Report"));
                    return;
                }

                self::sendReportInfo($player, $report, $session);
            }
        );

        $player->sendForm($form);
    }

    private static function sendReportInfo(Player $player, Report $report, Session $session): void
    {
        $textFormat = [
            '',
            '&7Report ID: &a' . $report->getId(),
            '&7Report Sender: &a' . $report->getSender(),
            '&7Report Reason: &a' . $report->getReason(),
            '&7Report DateTime: &a' . $report->getDateTime(),
            '',
            '&7Description: &f' . $report->getDescription(),
            ''
        ];

        $form = new MenuForm(
            "Report Info", TextFormat::colorize(implode("\n", $textFormat)),
            [
                new MenuOption(TextFormat::colorize("&cDelete"), new FormIcon("textures/ui/icon_trash", "path")),
                new MenuOption(TextFormat::colorize("&3Back"), new FormIcon("textures/ui/refresh", "path"))
            ],
            function (Player $player, int $option) use ($report, $session): void
            {
                switch ($option) {
                    case 0:
                    {
                        $session->removeReports($report->getId());
                        $player->sendMessage(TextFormat::colorize("&aReport ID : &e" . $report->getId() . "&a Successfully removed"));
                        return;
                    }
                    case 1:
                    {
                        self::sendPlayerStory($player, $session);
                        return;
                    }
                }
            }
        );

        $player->sendForm($form);
    }

    public static function sendMakeReport(Player $player): void
    {
        $sessionList = array_keys(SessionFactory::getAll());
        $reportReasons = Loader::getInstance()->getReportReasons();

        unset($sessionList[array_search($player->getName(), $sessionList)]);

        $form = new CustomForm(
            "Make Report",
            [
                new Dropdown("session", "Select Player", $sessionList, 0),
                new Dropdown("reason", "Select Reason", $reportReasons, 0),
                new Input("description", "Put Report Description", "Example: He's Flying")
            ],
            function (Player $player, CustomFormResponse $response) use ($sessionList, $reportReasons): void
            {
                $sessionKey = $response->getInt("session");
                $reasonKey = $response->getInt("reason");
                $description = $response->getString("description");

                $session = SessionFactory::get($sessionList[$sessionKey]);
                $reason = $reportReasons[$reasonKey] ?? null;

                if ($session === null || empty($reason) || empty($description)) {
                    $player->sendMessage(TextFormat::colorize("&cCan't realize this report"));
                    return;
                }

                $newReport = new Report(
                    Report::randomID($session), $reason, $player->getName(), date("j F, Y g:i A"), $description
                );

                $session->addReport($newReport);
                DiscordUtils::sendReportMessage($session, $newReport);

                $player->sendMessage(TextFormat::colorize("&aReport successfully send!"));
            }
        );

        $player->sendForm($form);
    }
}