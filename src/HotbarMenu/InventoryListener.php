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


}
