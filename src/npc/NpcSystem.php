<?php
declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use ckgcam\chocbar\Main;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use pocketmine\nbt\tag\CompoundTag;
use ckgcam\chocbar\npc\HumanNPC;

class NpcSystem {

    private Main $plugin;

    /**
     * @var array<string, array<string, HumanNPC>>
     * Format: $spawnedNpcs[playerName][npcId] = HumanNPC instance
     */
    private array $spawnedNpcs = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    private function Logger(string $message): void {
        $this->plugin->getLogger()->info(TextFormat::YELLOW . "[NPC System]" . TextFormat::GREEN ." > ". TextFormat::WHITE ."[". $message . "]");
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position, string $nametag, string $npcId): void {
        $name = $player->getName();

        $this->Logger("Spawning NPC For $name at {$world->getDisplayName()} @ {$position->getX()}, {$position->getY()}, {$position->getZ()} | Nametag: $nametag | ID: $npcId");

        // Check if this specific NPC has already been spawned for the player
        if (isset($this->spawnedNpcs[$name][$npcId]) && !$this->spawnedNpcs[$name][$npcId]->isClosed()) {
            $this->Logger("NPC '$npcId' already spawned for $name");
            return;
        }

        $this->Logger("Loading skin...");
        $skin = $this->loadSkin("test") ?? new Skin("fallback", str_repeat("\x00", 8192));
        $location = new Location($position->getX(), $position->getY(), $position->getZ(), $world, 0.0, 0.0);

        $this->Logger("Instantiating HumanNPC...");
        $npc = new HumanNPC($location, $skin);
        $this->Logger("Setting new npc's id");
        $npc->setNpcId($npcId);

        $npc->setNameTag($nametag);
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);

        $this->Logger("Spawning NPC to player...");
        $npc->spawnTo($player);

        $this->Logger("Tracking NPC '$npcId' for $name");
        $this->spawnedNpcs[$name][$npcId] = $npc;
    }

    public function despawnHubNPC(Player $player, ?string $npcId = null): void {
        $name = $player->getName();

        if (!isset($this->spawnedNpcs[$name])) {
            $this->Logger("No NPCs tracked for $name");
            return;
        }

        if ($npcId !== null) {
            $npc = $this->spawnedNpcs[$name][$npcId] ?? null;

            if ($npc instanceof HumanNPC) {
                if (!$npc->isClosed()) {
                    $npc->flagForDespawn(); // more graceful than close()
                    $this->Logger("Despawned NPC '$npcId' for $name");
                } else {
                    $this->Logger("NPC '$npcId' was already closed for $name");
                }
                unset($this->spawnedNpcs[$name][$npcId]);
            } else {
                $this->Logger("NPC '$npcId' not found for $name");
            }

            // Clean up the array if empty
            if (empty($this->spawnedNpcs[$name])) {
                unset($this->spawnedNpcs[$name]);
            }

        } else {
            foreach ($this->spawnedNpcs[$name] as $id => $npc) {
                if ($npc instanceof HumanNPC && !$npc->isClosed()) {
                    $npc->flagForDespawn();
                    $this->Logger("Despawned NPC '$id' for $name");
                }
            }

            unset($this->spawnedNpcs[$name]);
        }
    }

    public function loadSkin(string $name): ?Skin {
        $path = $this->plugin->getDataFolder() . "skins/" . $name . ".png";

        if (!file_exists($path)) {
            $this->Logger("Skin file not found at $path");
            return null;
        }

        $skinImage = @imagecreatefrompng($path);
        if ($skinImage === false) {
            $this->Logger("Failed to create image from $path");
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
