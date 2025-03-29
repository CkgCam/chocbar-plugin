<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
use ckgcam\chocbar\HotbarMenu\Hotbars;

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

    public function ApplyHotbar(Player $player, array $slots): void
    {
        $name = $player->getName();
        $this->Logger("Applying hotbar for {$name}");

        $inventory = $player->getInventory();
        $inventory->clearAll();

        for ($i = 0; $i < 9; $i++) {
            $Currentslot = $slots[$i] ?? null;

            if ($Currentslot !== null) {
                $this->Logger("Slot {$i}: " . json_encode($Currentslot));

                $item = $this->getItemFromName($Currentslot["item"]);
                $item->setCustomName("§r" . $Currentslot["name"]);

                $inventory->setItem($i, $item);
            } else {
                $inventory->setItem($i, VanillaItems::AIR());
            }
        }

        // Force inventory update just in case
        $inventory->sendContents($player);
    }


    public function RemoveHotbar(Player $player): void
    {
        $name = $player->getName();
        unset($this->activeHotbars[$name]);

        $player->getInventory()->clearAll();
        $this->Logger("Removed hotbar for {$name}");
    }

    public function GetActiveHotbar(Player $player): void
    {
        // Your logic here
    }

    private function getItemFromName(string $name): Item
    {
        // Normalize to uppercase and underscores
        $method = strtoupper($name);
        $method = str_replace(" ", "_", $method);

        if (method_exists(VanillaItems::class, $method)) {
            return VanillaItems::$method();
        }

        // Default to AIR if not found
        return VanillaItems::AIR();
    }
}
