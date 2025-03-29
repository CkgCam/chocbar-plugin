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
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use pocketmine\player\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use ckgcam\chocbar\npc\HumanNPC;

class EventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    private function Logger(String $message): void
    {
        $this->plugin->getLogger()->info(TextFormat::AQUA."[Event Listener]" . TextFormat::GREEN . "|" . TextFormat::WHITE . "[" . $message . "]");
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->Logger( "Player joined: " . $player->getName());

        if ($this->plugin->getServerType() === "hub") {
            $hub = $this->plugin->getHub();
            if ($hub !== null) {
                $hub->WhenPlayerJoins($player); // ✅ call the updated method
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $this->Logger( "Player quit: " . $player->getName());

        if ($this->plugin->getServerType() === "hub") {
            $hub = $this->plugin->getHub();
            if ($hub !== null) {
                $hub->onPlayerQuitEvent($player);
            }
        }
    }

    public function onNpcTap(EntityDamageByEntityEvent $event): void {
        $this->Logger("EntityDamageByEntityEvent fired");

        $damager = $event->getDamager();
        $entity = $event->getEntity();

        $this->Logger("Damager NameTag: {$damager->getNameTag()} | Entity NameTag: {$entity->getNameTag()}");
        $this->Logger("Entity class: " . get_class($entity));

        if (!$damager instanceof Player) {
            $this->Logger("Damager is NOT a Player");
            return;
        }

        if ($entity instanceof HumanNPC)
        {
        $npcId = $entity->getNpcId();
        $event->cancel();
        $this->Logger("Player Tapped Npc With ID: " . $npcId . " Cancelled Dmage Event On NPC");
        $this->plugin->onNpcTapped($damager, $npcId);
        }
        else
        {
            $this->Logger("Not An NPC");
        }
    }






    // Block Ticking Disabled Events
    public function onBlockUpdate(BlockUpdateEvent $event): void
    {
        $this->Logger("onBlockUpdate");
    }

    public function onBlockSpread(BlockSpreadEvent $event): void {
        $this->Logger("onBlockSpread");
        $source = $event->getSource();
        if ($this->plugin->isBlockTickingDisabled() || $source instanceof \pocketmine\block\Lava || $source instanceof \pocketmine\block\Water) {
            $event->cancel();
            $this->Logger("onBlockSpread canceled");
        }
    }

    public function onLeavesDecay(LeavesDecayEvent $event): void {
        $this->Logger("onLeavesDecay");
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
            $this->Logger("onLeavesDecay canceled");
        }
    }

    public function onBlockBurn(BlockBurnEvent $event): void {
        $this->Logger("onBlockBurn");
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
            $this->Logger("onBlockBurn canceled");
        }
    }

    public function onBlockGrow(BlockGrowEvent $event): void {
        $this->Logger("onBlockGrow");
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
            $this->Logger("onBlockGrow canceled");
        }
    }

    public function onBlockForm(BlockFormEvent $event): void {
        $this->Logger("onBlockForm");
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
            $this->Logger("onBlockForm canceled");
        }
    }

    public function onEntityTrampleFarmland(EntityTrampleFarmlandEvent $event): void {
        $this->Logger("onEntityTrampleFarmland");
        if ($this->plugin->isBlockTickingDisabled()) {
            $event->cancel();
            $this->Logger("onEntityTrampleFarmland canceled");
        }
    }

    // Explosion Events
    public function onEntityPreExplode(EntityPreExplodeEvent $event): void {
        $this->Logger("onEntityPreExplode");
        $event->setBlockBreaking(false);
        $entity = $event->getEntity();
        if ($entity !== null) {
            $entity->close();
        }
    }
}
