<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\item\enchantment\Enchantments;
use pocketmine\item\enchantment\EnchantmentInstance;
use ckgcam\chocbar\HotbarMenu\Hotbars;
use ckgcam\chocbar\HotbarMenu\HotbarSlot;

class HotbarMenuManager
{
    private $plugin;

    /** @var array<string, Hotbars> */
    private array $activeHotbars = [];

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    private function Logger(string $message): void
    {
        $this->plugin->getLogger()->info(TextFormat::YELLOW . "[HotbarMenuManager]" . TextFormat::GREEN . "|" . TextFormat::WHITE . "[{$message}]");
    }

    public function enable(): void
    {
        $this->Logger("Hotbar Menu loaded");
    }

    public function ApplyHotbar(Player $player, Hotbars $hotbar): void
    {
        $name = $player->getName();
        $this->Logger("Applying hotbar to {$name}");

        $this->activeHotbars[$name] = $hotbar;
        $inventory = $player->getInventory();
        $inventory->clearAll();

        $slots = $hotbar->getSlots();

        for ($i = 0; $i < 9; $i++) {
            $slot = $slots[$i] ?? null;

            if ($slot instanceof HotbarSlot) {
                $item = ItemFactory::getInstance()->get($slot->itemId);
                $item->setCustomName("§r" . $slot->name);

                if ($slot->enchanted) {
                    $dummyEnchant = Enchantments::UNBREAKING();
                    $item->addEnchantment(new EnchantmentInstance($dummyEnchant, 1));
                }

                // Optionally store action ID in lore for debugging or lookup
                $item->setLore(["§7Action ID: " . $slot->actionId]);

                $inventory->setItem($i, $item);
            } else {
                $inventory->setItem($i, VanillaItems::AIR());
            }
        }
    }

    public function RemoveHotbar(Player $player): void
    {
        $name = $player->getName();
        unset($this->activeHotbars[$name]);

        $player->getInventory()->clearAll();
        $this->Logger("Removed hotbar for {$name}");
    }

    public function GetActiveHotbar(Player $player): ?Hotbars
    {
        return $this->activeHotbars[$player->getName()] ?? null;
    }
}
