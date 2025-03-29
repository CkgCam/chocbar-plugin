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
use pocketmine\event\entity\EntityDamageByEntityEvent;

class EventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    private function Logger(String $message): void
    {
        $this->plugin->getLogger()->info("[Chocbar] [Event Listener] | [" . $message . "]");
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
        $this->Logger("entity damage event fired");
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        $this->Logger("Damager NameTag: " . $damager->getNameTag() . "enity NameTag: " . $entity->getNameTag());

        if (!$damager instanceof Player){
            $this->Logger("Damager Is Not An Instance Of Player");
            return;
        }
        if (!$entity instanceof Human){
            $this->Logger("Damager Is Not An Instance Of Human");$this->Logger("Damager Is Not An Instance Of Human");
            return; // <-- use Human if you're only targeting Human-type NPCs
        }

        $id = $entity->getNetworkProperties()->getString(100, null);
        if ($id !== null) {
            $this->Logger("Damager has id cancelling damage event");
            $event->cancel(); // block damage
            $this->Logger("Passing info of tapped npc to main");
            $this->plugin->onNpcTapped($damager, $id); // trigger hook
        }
        else{
            $this->Logger("Damager Dose Not Have an id");
        }
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
