<?php

declare(strict_types=1);

namespace ckgcam\chocbar\survival;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ckgcam\chocbar\Main;
use ckgcam\chocbar\bossbar\BossBarManager;

class Survival {

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
        $this->plugin->getLogger()->info(TextFormat::GREEN . "chocbar Surival Manager loaded!");
    }

    public function onPlayerJoined(Player $player): void
    {
        $this->bossBarManager->showBossBar($player, "Survival Mode | /menu for more");
    }

}
