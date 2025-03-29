<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
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
        $this->plugin->getLogger()->info(TextFormat::YELLOW . "[HotbarMenuManager]" . TextFormat::GREEN . "|" . TextFormat::WHITE . "[" . $message . "]");
    }

    public function enable(): void
    {
        $this->Logger("Hotbar Menu loaded");
    }

    public function ApplyHotbar(Player $player, Hotbars $hotbar): void
    {
        $name = $player->getName();
        $this->Logger("Applying hotbar to $name");

        $this->activeHotbars[$name] = $hotbar;
        $inv = $player->getInventory();
        $inv->clearAll();

        $slots = $hotbar->getSlots();

        for ($i = 0; $i < 9; $i++) {
            $slot = $slots[$i] ?? null;

            if ($slot instanceof HotbarSlot) {
                $item = ItemFactory::getInstance()->get($slot->itemId);
                $item->setCustomName("§r" . $slot->name);

                if ($slot->enchanted) {
                    // Add dummy enchant for visual glint
                    $item->addEnchantment(VanillaItems::ENCHANTED_BOOK()->getEnchantment(0) ?? null);
                }

                // Optionally: store $slot->actionId in lore or NBT

                $inv->setItem($i, $item);
            } else {
                $inv->setItem($i, VanillaItems::AIR());
            }
        }
    }

    public function RemoveHotbar(Player $player): void
    {
        $name = $player->getName();
        unset($this->activeHotbars[$name]);
        $player->getInventory()->clearAll();
        $this->Logger("Removed hotbar for $name");
    }

    public function GetActiveHotbar(Player $player): ?Hotbars
    {
        return $this->activeHotbars[$player->getName()] ?? null;
    }
}
