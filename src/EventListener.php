<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use ckgcam\chocbar\HotbarMenu\HotbarMenuManager;
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

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;

use pocketmine\block\Farmland;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use pocketmine\player\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use ckgcam\chocbar\npc\HumanNPC;

class EventListener implements Listener {

    private Main $plugin;

    private HotbarMenuManager $hotbarMenuManager;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function enable(): void
    {
        $this->hotbarMenuManager = $this->plugin->getScript("HotbarMenuManager");
    }

    private function Logger(String $message): void
    {
        $this->plugin->getLogger()->info(TextFormat::AQUA."[Event Listener]" . TextFormat::GREEN . " > " . TextFormat::WHITE . "[" . $message . "]");
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->Logger( "Player joined: " . $player->getName());

        if ($this->plugin->getServerType() === "hub") {
            $hub = $this->plugin->getHub();
            if ($hub !== null) {
                $hub->OnPlayerJoin($player); // ✅ call the updated method
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $this->Logger( "Player quit: " . $player->getName());
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


    //Inventory ish events
    public function onInventoryTransaction(InventoryTransactionEvent $event): void {
        foreach ($event->getTransaction()->getActions() as $action) {
            if ($action instanceof SlotChangeAction) {
                $inventory = $action->getInventory();
                $holder = $inventory->getHolder();

                if ($holder instanceof Player) {
                    $player = $holder;

                    //Do thingy here
                    if($this->hotbarMenuManager !== null)
                    {
                        $this->hotbarMenuManager->OnInventoryEvent($player, $event);
                    }

                    // No need to keep checking once player found
                    break;
                }
            }
        }
    }


    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        // cancel dropping items
        $player = $event->getPlayer();
        if($this->hotbarMenuManager !== null)
        {
            $this->hotbarMenuManager->OnInventoryEvent($player, $event);
        }
    }

    public function onPlayerItemHeld(PlayerItemHeldEvent $event): void {
        // cancel changing selected hotbar slot
        $player = $event->getPlayer();
        if($this->hotbarMenuManager !== null)
        {
            $this->hotbarMenuManager->OnInventoryEvent($player, $event);
        }
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event): void {
        // cancel using item (right click, etc.)
        $player = $event->getPlayer();
        if($this->hotbarMenuManager !== null)
        {
            $this->hotbarMenuManager->OnInventoryEvent($player, $event);
        }
    }






    // Block Ticking Disabled Events
    public function onBlockUpdate(BlockUpdateEvent $event): void
    {

    }

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
