<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

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

    public function getActiveHotbar(Player $player): void
    {
        // You can implement logic here if needed
    }

    /**
     * Resolves a VanillaItems method from a string like "compass" or "iron_axe"
     */
    private function resolveItemFromName(string $name): Item
    {
        $method = strtoupper(str_replace(" ", "_", $name));

        if (method_exists(VanillaItems::class, $method)) {
            return VanillaItems::$method();
        }

        $this->log("Unknown item name '{$name}', defaulting to AIR");
        return VanillaItems::AIR();
    }

    private function log(string $message): void
    {
        $this->plugin->getLogger()->info(
            TextFormat::YELLOW . "[HotbarMenuManager]" .
            TextFormat::GREEN . " | " .
            TextFormat::WHITE . "[{$message}]"
        );
    }
}
