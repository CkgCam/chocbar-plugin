<?php

declare(strict_types=1);

namespace ckgcam\chocbar\hub;

use ckgcam\chocbar\bossbar\BossBarManager;
use ckgcam\chocbar\Main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\ClosureTask;

class Hub {
    private Main $plugin;

    private BossBarManager $bossBarManager;

    public function setBossBarManager(BossBarManager $bossBarManager): void {
        $this->bossBarManager = $bossBarManager;
    }

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function enable(): void
    {
        $this->plugin->getLogger()->info(TextFormat::GREEN . "chocbar Hub Manager loaded!");

        $this->plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void {
            foreach ($this->plugin->getServer()->getWorldManager()->getWorlds() as $world) {
                $world->setTime(6000);
                $world->stopTime();
            }
        }), 100);

        $this->plugin->getLogger()->info(TextFormat::GREEN . "Time locked to midday in Hub worlds.");
    }

    public function onPlayerJoined(Player $player): void
    {
        $this->bossBarManager->showBossBar($player, "Chocbar Hub | /menu for more");
    }
}
