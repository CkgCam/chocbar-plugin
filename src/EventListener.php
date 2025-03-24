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
    BlockPlaceEvent,
    FarmlandHydrationChangeEvent
};
use pocketmine\event\entity\{
    EntityPreExplodeEvent,
    EntityTrampleFarmlandEvent
};
use pocketmine\block\{Farmland, Lava, Water};
use pocketmine\player\Player;

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

    // Block Ticking Disabled Events
    public function onBlockUpdate(BlockUpdateEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled() || $event->getBlock() instanceof Farmland) {
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

    public function onFarmlandHydrationChange(FarmlandHydrationChangeEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->setNewHydration(7); // Force max hydration
            $event->cancel(); // Prevent dirt conversion
        }
    }

    public function onEntityTrampleFarmland(EntityTrampleFarmlandEvent $event): void {
        if ($this->plugin->isBlockTickingDisabled()) {
            // Cancel trampling so farmland doesn't turn into dirt
            $event->cancel();
        }
    }

    // Disable Building Events
    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if (!$this->plugin->getServer()->isOp($player->getName())) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();

        // Safety check: Make sure there’s at least one block being placed
        // Convert Generator to array so we can count it
        $blocks = iterator_to_array($event->getTransaction()->getBlocks());
        if (count($blocks) === 0) {
            $player->sendMessage("§c[Debug] No blocks detected in transaction.");
            return;
        }

        if (!$this->plugin->getServer()->isOp($player->getName())) {
            $event->cancel();
            $player->sendMessage("§cYou're not allowed to place blocks.");
        } else {
            $player->sendMessage("§a[Debug] Block place allowed for OP.");
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
