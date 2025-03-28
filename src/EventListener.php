<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\{
    BlockUpdateEvent,
    BlockSpreadEvent,
    LeavesDecayEvent,
    BlockBurnEvent,
    BlockGrowEvent,
    BlockFormEvent,
    BlockBreakEvent,
    BlockPlaceEvent,
    FarmlandHydrationChangeEvent
};
use pocketmine\event\entity\{
    EntityPreExplodeEvent,
    EntityTrampleFarmlandEvent
};
use pocketmine\block\Farmland;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\World;
use pocketmine\player\Player;

class EventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        $this->plugin->getLogger()->info("[chocbar] EventListener: Player joined: " . $player->getName());

        if ($this->plugin->getServerType() === "hub") {
            $hub = $this->plugin->getHub();
            if ($hub !== null) {
                $hub->onPlayerJoinEvent($player); // ✅ call the updated method
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        unset($this->plugin->getNpcSystem()->spawnedFor[$event->getPlayer()->getName()]);
    }



    // Block Ticking Disabled Events
    public function onBlockUpdate(BlockUpdateEvent $event): void {}

    public function onBlockSpread(BlockSpreadEvent $event): void {
        $source = $event->getSource();
        if ($this->plugin->isBlockTickingDisabled() || $source instanceof \pocketmine\block\Lava || $source instanceof \pocketmine\block\Water) {
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

    public function onEntityTrampleFarmland(EntityTrampleFarmlandEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
        }
    }

    // Explosion Events
    public function onEntityPreExplode(EntityPreExplodeEvent $event): void {
        $event->setBlockBreaking(false);
        $entity = $event->getEntity();
        if ($entity !== null) {
            $entity->close();
        }
    }
}
