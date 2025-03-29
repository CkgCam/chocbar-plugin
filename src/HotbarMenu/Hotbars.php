<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

class HotbarSlot {
    public function __construct(
        public string $name,
        public string $itemId,         // Example: "minecraft:compass"
        public bool $enchanted,
        public string $actionId        // Custom action hook ID
    ) {}
}

class HubHotbar {
    /** @return HotbarSlot[] */
    public static function getSlots(): array {
        return [
            new HotbarSlot("Navigator", "minecraft:compass", false, "openNaviForm"),
            new HotbarSlot("Info Book", "minecraft:book", false, "openInfo"),
        ];
    }
}

class SurvivalHotbar {
    public static function getSlots(): array {
        return [
            new HotbarSlot("Starter Axe", "minecraft:iron_axe", true, "axeAction"),
            new HotbarSlot("Starter Pickaxe", "minecraft:iron_pickaxe", true, "pickaxeAction"),
        ];
    }
}
