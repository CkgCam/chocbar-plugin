<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class HotbarMenuManager {

    private $plugin;
    private array $playerHudsAttached = [];

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function enable(): void {
        $this->plugin->getLogger()->info(TextFormat::GREEN . "chocbar Hotbar Menu loaded!");
    }

    // ✅ Attach HUD to a player
    public function AttachHud(Player $player, array $hotbarConfig, string $type): void {
        if (!isset($hotbarConfig[$type]["items"])) {
            $this->plugin->getLogger()->warning("Hotbar config for type '{$type}' not found!");
            return;
        }

        // Store the player's HUD type
        $this->addAttachedPlayer($player, $type);

        foreach ($hotbarConfig[$type]["items"] as $slotKey => $identifier) {
            $slotNumber = (int) str_replace("slot", "", $slotKey);
            $customName = $hotbarConfig[$type]["names"][$slotKey] ?? "";
            $item = $this->getItemFromIdentifier($identifier, $customName);
            $player->getInventory()->setItem($slotNumber, $item);
        }
    }

    // ✅ Detach HUD from a player
    public function DetachHud(Player $player): void {
        $this->plugin->getLogger()->info(TextFormat::GREEN . "Detaching Hud!");

        if (!$this->isPlayerAttached($player)) {
            return;
        }

        $hudType = $this->getPlayersAttachedHudType($player);
        if ($hudType === null) {
            return;
        }

        // Clear the player's HUD items
        if (isset($this->plugin->getConfig()->get("hotbar", [])[$hudType]["items"])) {
            foreach ($this->plugin->getConfig()->get("hotbar", [])[$hudType]["items"] as $slotKey => $identifier) {
                $slotNumber = (int) str_replace("slot", "", $slotKey);
                $player->getInventory()->setItem($slotNumber, VanillaItems::AIR());
            }
        }

        // Remove player from attached list
        $this->removeAttachedPlayer($player);
    }

    // ✅ Get item from identifier
    public function getItemFromIdentifier(string $identifier, string $itemDisplayName): Item {
        switch (strtolower($identifier)) {
            case "navi":
                $item = VanillaItems::COMPASS();
                break;
            case "air":
                $item = VanillaItems::AIR();
                break;
            default:
                $item = VanillaItems::AIR();
        }

        if ($itemDisplayName !== "") {
            $item->setCustomName($itemDisplayName);
        }

        return $item;
    }

    // ✅ Handle HUD interaction
    public function handleHudInteraction(Player $player, int $slot): void {
        $hotbarConfig = $this->plugin->getConfig()->get("hotbar", []);
        $type = $this->getPlayersAttachedHudType($player);

        if ($type !== null && isset($hotbarConfig[$type]["actions"]["slot" . $slot])) {
            $ID = $hotbarConfig[$type]["actions"]["slot" . $slot];
            if (!empty($ID)) {
                $this->plugin->executeHotbarActions($player, $ID);
            }
        }
    }

    // ✅ Helper: Add player to HUD attachment list
    public function addAttachedPlayer(Player $player, string $hudType): void {
        $this->playerHudsAttached[$player->getName()] = $hudType;
    }

    // ✅ Helper: Remove player from HUD attachment list
    public function removeAttachedPlayer(Player $player): void {
        unset($this->playerHudsAttached[$player->getName()]);
    }

    // ✅ Helper: Check if player has an attached HUD
    public function isPlayerAttached(Player $player): bool {
        return isset($this->playerHudsAttached[$player->getName()]);
    }

    // ✅ Helper: Get player's attached HUD type
    public function getPlayersAttachedHudType(Player $player): ?string {
        return $this->playerHudsAttached[$player->getName()] ?? null;
    }

    // ✅ Helper: Get all attached players
    public function getAttachedPlayers(): array {
        return $this->playerHudsAttached;
    }
}
