<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\block\BlockBurnEvent;

class EventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $hotbarConfig = $this->plugin->getConfig()->get("hotbar");

        $this->plugin->getHotbarMenuManager()->AttachHud($player, $hotbarConfig, "hub");

        $serverType = $this->plugin->getServerType();
        $survival = $this->plugin->getSurvival();

        if ($serverType === "survival" && $survival !== null) {
            $survival->onPlayerJoined($player);
        }
    }

    public function onBlockUpdate(BlockUpdateEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
        }
    }

    public function onBlockSpread(BlockSpreadEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
        }
    }

    public function onLeavesDecay(LeavesDecayEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
        }
    }

    public function onBlockBurn(BlockBurnEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
        }
    }
}
