<?php
declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use ckgcam\chocbar\Main;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\utils\Utils;

class NpcSystem {

    private Main $plugin;

    /** @var array<string, Human> */
    private array $spawnedNpcs = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position): void {
        $name = $player->getName();

        if (isset($this->spawnedNpcs[$name]) && !$this->spawnedNpcs[$name]->isClosed()) {
            $this->plugin->getLogger()->info("[chocbar] NPC already spawned for $name");
            return;
        }

        $this->plugin->getLogger()->info("[chocbar] Spawning NPC for $name");

        $skin = $this->loadSkin("test") ?? new Skin("fallback", str_repeat("\x00", 8192));
        $location = new Location($position->getX(), $position->getY(), $position->getZ(), $world, 0, 0);
        $npc = new Human($location, $skin);

        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);

        $npc->spawnTo($player); // only show to that player

        $this->spawnedNpcs[$name] = $npc;
    }

    public function despawnHubNPC(Player $player): void {
        $name = $player->getName();

        if (isset($this->spawnedNpcs[$name])) {
            $npc = $this->spawnedNpcs[$name];
            if (!$npc->isClosed()) {
                $npc->close(); // remove from the world
            }
            unset($this->spawnedNpcs[$name]); // free memory
            $this->plugin->getLogger()->info("[chocbar] Despawned NPC for $name");
        }
    }

    public function loadSkin(string $name): ?Skin {
        $path = $this->plugin->getDataFolder() . "skins/" . $name . ".png";

        if (!file_exists($path)) {
            $this->plugin->getLogger()->warning("Skin file not found at $path");
            return null;
        }

        $skinBytes = @file_get_contents($path);
        if ($skinBytes === false) {
            $this->plugin->getLogger()->warning("Failed to read skin file $path");
            return null;
        }

        $skinImage = @imagecreatefrompng($path);
        if ($skinImage === false) {
            $this->plugin->getLogger()->warning("Failed to create image from $path");
            return null;
        }

        $width = imagesx($skinImage);
        $height = imagesy($skinImage);

        $skinData = "";
        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                $color = imagecolorat($skinImage, $x, $y);
                $a = 127 - (($color >> 24) & 0x7F);
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;
                $skinData .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        imagedestroy($skinImage);

        return new Skin("custom.skin", $skinData);
    }



}
