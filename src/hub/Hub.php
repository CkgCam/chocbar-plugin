<?php

declare(strict_types=1);

namespace ckgcam\chocbar\hub;

use ckgcam\chocbar\bossbar\BossBarManager;
use ckgcam\chocbar\Main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

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
    }

    public function onPlayerJoined(Player $player): void
    {
        $this->bossBarManager->showBossBar($player, "Chocbar Hub | /menu for more");
    }
}