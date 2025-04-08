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

        // Auto-extract if missing
        if (!file_exists($skinPath)) {
            $this->plugin->saveResource("skins/{$name}.png");
        }

        $this->plugin->getLogger()->info("[SkinLoader] Loading skin image: $skinPath");

        $skinData = $this->convertSkinImageToBytes($skinPath);
        if (!in_array(strlen($skinData), [8192, 16384, 32768, 65536])) {
            $this->plugin->getLogger()->warning(TextFormat::RED . "[SkinLoader] Invalid skin size " . strlen($skinData) . " for {$name}, using fallback 64x64 blank");
            $skinData = str_repeat("\x00", 8192);
        }

        return new Skin($name, $skinData);
    }

    private function convertSkinImageToBytes(string $path): string {
        $image = @imagecreatefrompng($path);
        if (!$image) return "";

        $width = imagesx($image);
        $height = imagesy($image);
        $bytes = "";

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                $color = imagecolorat($image, $x, $y);
                $a = 127 - (($color >> 24) & 0x7F);
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        imagedestroy($image);
        return $bytes;
    }
}
