<?php

declare(strict_types=1);

namespace ckgcam\chocbar\bossbar;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\types\BossBarColor;
use pocketmine\network\mcpe\protocol\types\BossBarDivision;

class BossBarManager {
    private array $playerBars = [];

    public function showBossBar(Player $player, string $text) {
        $packet = new BossEventPacket();
        $packet->bossActorUniqueId = $player->getId(); // ✅ Corrected entity ID
        $packet->eventType = BossEventPacket::TYPE_SHOW;
        $packet->title = $text;
        $packet->filteredTitle = $text; // ✅ Add this line to prevent a crash
        $packet->healthPercent = 1.0; // Full boss bar

        // ✅ Initialize missing properties
        $packet->darkenScreen = false; // Prevents game screen from darkening
        $packet->color = 0; // Default color
        $packet->overlay = 0; // No overlay effect

        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public function removeBossBar(Player $player): void
    {
        if (!isset($this->playerBars[$player->getName()])) return;

        $pk = new BossEventPacket();
        $pk->bossEid = $this->playerBars[$player->getName()];
        $pk->eventType = BossEventPacket::TYPE_HIDE;

        $player->getNetworkSession()->sendDataPacket($pk);
        unset($this->playerBars[$player->getName()]);
    }

}
