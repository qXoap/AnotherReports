<?php

namespace xoapp\reports\utils;

use Closure;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use xoapp\reports\Loader;

class TaskUtils
{
    public static function submitDelayed(Closure $closure, int $delay = 20): void
    {
        Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask($closure), $delay);
    }

    public static function submitRepeating(Closure|Task $task, int $ticks = 20): void
    {
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(
            $task instanceof Task ? $task : new ClosureTask($task), $ticks
        );
    }

    public static function submitAsync(AsyncTask $task): void
    {
        Server::getInstance()->getAsyncPool()->submitTask($task);
    }
}