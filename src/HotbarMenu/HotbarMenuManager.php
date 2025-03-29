<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class HotbarMenuManager
{

    private $plugin;

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
        $this->Logger("hocbar Hotbar Menu loaded");
    }

    public function ApplyHotbar()
    {

    }

    public function RemoveHotbar()
    {

    }
}