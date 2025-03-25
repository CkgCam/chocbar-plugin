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
        $block = $event->getBlock();
        if ($this->plugin->isBlockTickingDisabled() && $block instanceof Farmland) {
            $this->plugin->getLogger()->info("Restoring farmland from dirt at " . $block->getPosition()->__toString());
            $block->getPosition()->getWorld()->setBlock($block->getPosition(), VanillaBlocks::FARMLAND());
        }
    }


    public function onFarmlandHydrationChange(FarmlandHydrationChangeEvent $event): void {
        $block = $event->getBlock();
        $player = $event->getPlayer(); // Not all versions support this, so be careful

        // ✅ Debug: Check if event triggers
        $this->plugin->getLogger()->info("FarmlandHydrationChangeEvent triggered at " . $block->getPosition()->__toString());

        // ✅ Debug: Check old & new hydration values
        $this->plugin->getLogger()->info("Old Hydration: " . $event->getOldHydration() . " | New Hydration: " . $event->getNewHydration());

        if ($this->plugin->isBlockTickingDisabled()) {
            // ✅ Force hydration to max
            $event->setNewHydration(7);
            $this->plugin->getLogger()->info("Setting hydration to max (7) at " . $block->getPosition()->__toString());
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
