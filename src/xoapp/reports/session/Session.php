<?php

namespace xoapp\reports\session;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use xoapp\reports\Loader;
use xoapp\reports\object\Report;
use xoapp\reports\utils\TaskUtils;

class Session
{
    /** @var Report[] */
    private array $reports = [];

    private Config $playerData;

    public function __construct(
        private readonly string $xuid,
        private readonly string $name
    )
    {
        $this->playerData = new Config(Loader::getInstance()->getDataFolder() . "players/" . $this->name . ".json", Config::JSON);
    }

    private function load(): void
    {
        foreach ($this->playerData->getAll() as $id => $data) {
            $this->reports[$id] = new Report(
                $id, $data['reason'], $data['sender'], $data['dateTime'], $data['description']
            );
        }

        TaskUtils::submitDelayed(fn () => Loader::getInstance()->getLogger()->info(TextFormat::colorize(
            "&aLoaded &e" . count($this->reports) . "&a from player &e" . $this->name
        )));
    }

    public function getXuid(): string
    {
        return $this->xuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlayer(): ?Player
    {
        return Server::getInstance()->getPlayerExact($this->name);
    }

    public function getReports(): array
    {
        return $this->reports;
    }

    public function setReports(array $reports): void
    {
        $this->reports = $reports;
    }

    public function addReport(Report $report): void
    {
        $this->reports[$report->getId()] = $report;
    }

    public function getReport(string $id): ?Report
    {
        return $this->reports[$id] ?? null;
    }

    public function removeReports(string $id): void
    {
        unset($this->reports[$id]);
    }

    public function save(): void
    {
        if (empty($this->reports)) {
            $this->playerData->setAll([]);
            $this->playerData->save();
            return;
        }

        foreach ($this->reports as $id => $report) {
            $this->playerData->set($id, $report->toArray());
            $this->playerData->save();
        }
    }
}