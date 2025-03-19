<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\event\player\PlayerInteractEvent;
use ckgcam\chocbar\Main;

class InventoryListener implements Listener {

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event): void {
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();

        // Check if this player's HUD is attached
        $hotbarManager = $this->plugin->getHotbarMenuManager();

        if($hotbarManager->isPlayerAttached($player)) {
            $this->plugin->getLogger()->info("Player {$player->getName()} tried to move something, cancelling transaction");

            // Loop over each action in the transaction
            foreach ($transaction->getActions() as $action) {
                if ($action instanceof SlotChangeAction) {
                    $slot = $action->getSlot();
                    // If this is one of the protected hotbar slots (0 to 8), cancel the transaction
                    if ($slot >= 0 && $slot <= 8) {
                        $event->cancel();
                        return;
                    }
                }
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $hotbarManager = $this->plugin->getHotbarMenuManager();

        if ($hotbarManager->isPlayerAttached($player)) {
            return;
        }

        // Determine the slot of the item the player is currently holding.
        // (This example uses the held item index from the inventory.)
        $slot = $player->getInventory()->getHeldItemIndex();

        // Now, pass the player and slot number to your hotbar menu handler.
        $hotbarManager->handleHudInteraction($player, $slot);

        // Optionally, cancel the default interaction if needed.
        $event->cancel();
    }
}
