<?php

declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use pocketmine\entity\Skin;
use pocketmine\utils\TextFormat;
use ckgcam\chocbar\Main;

class SkinLoader {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function load(string $name): ?Skin {
        $skinPath = $this->plugin->getDataFolder() . "skins/{$name}.png";
        $geoPath = $this->plugin->getDataFolder() . "geo/{$name}.json";

        // Auto-extract if missing
        if (!file_exists($skinPath)) {
            $this->plugin->saveResource("skins/{$name}.png");
        }
        if (!file_exists($geoPath)) {
            $this->plugin->saveResource("geo/{$name}.json");
        }

        // Final existence check
        if (!file_exists($skinPath) || !file_exists($geoPath)) {
            $this->plugin->getLogger()->warning(TextFormat::RED . "[SkinLoader] Missing files for skin: {$name}");
            return null;
        }

        $skinData = file_get_contents($skinPath);
        $geoData = file_get_contents($geoPath);

        return new Skin(
            $name,
            $skinData,
            "",
            "geometry.{$name}",
            $geoData
        );
    }
}
