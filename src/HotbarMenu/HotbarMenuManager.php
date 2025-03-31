<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

use pocketmine\inventory\Inventory;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\event\Event;
use pocketmine\event\Cancellable;

class HotbarMenuManager
{
    private mixed $plugin;

    /** @var array<string, Hotbars> */
    private array $activeHotbars = [];

    public function __construct(mixed $plugin)
    {
        $this->plugin = $plugin;
    }

    public function enable(): void
    {
        $this->log("Hotbar Menu loaded");
    }

    /**
     * Applies a hotbar layout to the specified player.
     *
     * @param Player $player
     * @param array<int, array{name: string, item: string, enchanted?: bool}> $slots
     */
    public function applyHotbar(Player $player, array $slots): void
    {
        $name = $player->getName();
        $this->log("Applying hotbar for {$name}");


        // Track active hotbar
        $this->activeHotbars[$name] = [
            "slots" => $slots
        ];

        $inventory = $player->getInventory();
        $inventory->clearAll();

        for ($i = 0; $i < 9; $i++) {
            $slot = $slots[$i] ?? null;

            if ($slot !== null) {
                $this->log("Slot {$i}: " . json_encode($slot));

                $itemName = $slot["item"] ?? "air";
                $customName = $slot["name"] ?? ucfirst($itemName);
                $enchanted = $slot["enchanted"] ?? false;

                $item = $this->resolveItemFromName($itemName);
                $item->setCustomName("§r" . $customName);

                if ($enchanted) {
                    //$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
                }

                $inventory->setItem($i, $item);
            } else {
                $inventory->setItem($i, VanillaItems::AIR());
            }
        }
    }

    public function removeHotbar(Player $player): void
    {
        unset($this->activeHotbars[$player->getName()]);
        $player->getInventory()->clearAll();
        $this->log("Removed hotbar for {$player->getName()}");
    }

    //Check if the player currently has a hotbar attached
    public function hasHotbar(Player $player): bool
    {
        return isset($this->activeHotbars[$player->getName()]);
    }

    //Called whenever these an inventory event
    public function CancelInvEvent(Player $player, Event $event): void
    {
        //Make sure the event passed in is able to be canceld otherwise ignore
        if (!$event instanceof Cancellable) {
            $this->log("Unable to cancel inventory event something went wrong");
            return;
        }


        if ($this->hasHotbar($player)) {
            $event->cancel();
            $this->log("Inventory event for {$player->getName()} has been canceled");
        }
    }

    public function OnUseItemEvent(Player $player, Event $event): void
    {
        if (!$event instanceof Cancellable) {
            $this->log("Unable to cancel inventory event something went wrong");
            return;
        }

        if ($this->hasHotbar($player)) {
            $item = $event->getItem();
            $slot = $player->getInventory()->getHeldItemIndex(); // Hotbar slot 0–8

            // Get the slot data
            $slotData = $this->activeHotbars[$player->getName()]["slots"][$slot] ?? null;

            CallFuncForItem($slotData["call_id"] ?? "failed");

            $event->cancel();
            $this->log("Inventory event for {$player->getName()} has been canceled");
        }
    }

    public function CallFuncForItem(Player $player, String $id): void
    {
        switch ($id) {
            case "openNavi":
                $player->sendMessage(TextFormat::GREEN . "Navi open");
                break;
            case "openBook":
                $player->sendMessage(TextFormat::GREEN . "Book open");
                break;
            case "openShop":
                    $player->sendMessage(TextFormat::GREEN . "Shop open");
                break;
                case "failed":
                    $player->sendMessage(TextFormat::RED . "Failed To Pull Call Id From This Item");
                    break;
                default:
                    $player->sendMessage(TextFormat::RED . "Unknown id");
                    break;

        }
    }
    /**
     * Resolves a VanillaItems method from a string like "compass" or "iron_axe"
     */
    private function resolveItemFromName(string $name): Item
    {
        $method = strtoupper(str_replace(" ", "_", $name));

        if (is_callable([VanillaItems::class, $method])) {
            return call_user_func([VanillaItems::class, $method]);
        }

        $this->log("Unknown item name '{$name}', defaulting to AIR");
        return VanillaItems::AIR();
    }


    private function log(string $message): void
    {
        $this->plugin->getLogger()->info(
            TextFormat::YELLOW . "[HotbarMenuManager]" .
            TextFormat::GREEN . " > " .
            TextFormat::WHITE . "[{$message}]"
        );
    }
}
