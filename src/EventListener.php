<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\block\{
    BlockUpdateEvent,
    BlockSpreadEvent,
    LeavesDecayEvent,
    BlockBurnEvent,
    BlockGrowEvent,
    BlockFormEvent,
    BlockBreakEvent,
    BlockPlaceEvent
};
use pocketmine\event\entity\EntityPreExplodeEvent;
use pocketmine\block\{Farmland, Lava, Water, VanillaBlocks};
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\Server;

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
        $hub = $this->plugin->getHub();

        if ($serverType === "survival" && $survival !== null) {
            $survival->onPlayerJoined($player);
        }
        if ($serverType === "hub" && $hub !== null) {
            $hub->onPlayerJoined($player);
        }
    }

    public function onBlockUpdate(BlockUpdateEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
            return;
        }

        if ($event->getBlock() instanceof Farmland) {
            $event->cancel();
        }
    }

    public function onBlockSpread(BlockSpreadEvent $event): void {
        $source = $event->getSource();
        if ($this->plugin->isBlockTickingDisabled() || $source instanceof Lava || $source instanceof Water) {
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

    public function onBlockGrow(BlockGrowEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
        }
    }

    public function onBlockForm(BlockFormEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if (!$player->hasPermission("chocbar.build")) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        if (!$player->hasPermission("chocbar.build")) {
            $event->cancel();
        }
    }

    public function onEntityPreExplode(EntityPreExplodeEvent $event): void {
        $event->setBlockBreaking(false);
        $entity = $event->getEntity();
        if ($entity !== null) {
            $entity->close();
        }
    }
}
