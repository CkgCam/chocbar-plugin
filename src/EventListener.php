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
            $player = $event->getPlayer(); // Not all versions pass a player, so be careful
            $block = $event->getBlock();

            // Debug message to check if event fires
            if ($player instanceof Player) {
                $player->sendMessage("§e[Debug] Farmland hydration triggered at " . $block->getPosition()->__toString());
            }

            $event->setNewHydration(7); // Force max hydration
            //$event->cancel();  // This might be needed to fully prevent hydration changes

            // Debug confirmation
            if ($player instanceof Player) {
                $player->sendMessage("§a[Debug] Hydration set to max (7)");
            }
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

        if (!$this->plugin->getServer()->isOp($player->getName())) {
            $event->cancel();
            $player->sendMessage("§cYou're not allowed to place blocks.");
        } else {
            foreach ($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]) {
                $player->sendMessage("§7[Debug] Placing: " . $block->getName() . " at $x, $y, $z");
            }
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
