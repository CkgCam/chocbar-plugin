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

    private function Logger(String $message): void
    {
        $this->plugin->getLogger()->info(TextFormat::YELLOW."[NPC System]" . TextFormat::GREEN . "|" . TextFormat::WHITE . "[" . $message . "]");
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position, string $nametag, string $npcId): void {
        $name = $player->getName();

        $this->Logger("Spawning NPC For Player: ".
            $name .
        " At World: " .
        $world->getDisplayName() .
        " With Cords: " .
            $position->getX() . " " . $position->getY() . " " . $position->getZ() .
        " | Nametag: " .
            $nametag .
            " | NPCId: " .
            $npcId);

        // Check if this specific NPC has already been spawned for the player
        if (isset($this->spawnedNpcs[$name][$npcId]) && !$this->spawnedNpcs[$name][$npcId]->isClosed()) {
            $this->Logger( "NPC '$npcId' already spawned for $name");
            return;
        }

        Logger("Spawning NPC '$npcId' for $name");

        Logger("Loading Skin....");
        $skin = $this->loadSkin("test") ?? new Skin("fallback", str_repeat("\x00", 8192));
        $location = new Location($position->getX(), $position->getY(), $position->getZ(), $world, 0, 0);
        Logger("Adding Nbt CompoundTag '$npcId' under npc_id");
        $nbt = CompoundTag::create()->setString("npc_id", $npcId);
        Logger("Creating Npc...");
        $npc = new HumanNPC($location, $skin, $nbt);


        Logger("Setting NPC NameTag Too: ". $nametag);
        $npc->setNameTag($nametag);
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);

        Logger("SpawnTo: " . $player);
        $npc->spawnTo($player);

        // Track this NPC by player + id
        Logger("Added [" .  $name . " | " . $npcId . "] To spawnedNpcs List Keeping Track...");
        $this->spawnedNpcs[$name][$npcId] = $npc;
    }

    public function despawnHubNPC(Player $player, string $npcId = null): void {
        $name = $player->getName();

        if ($npcId !== null) {
            if (isset($this->spawnedNpcs[$name][$npcId])) {
                $npc = $this->spawnedNpcs[$name][$npcId];
                if (!$npc->isClosed()) {
                    $npc->close();
                }
                unset($this->spawnedNpcs[$name][$npcId]);
                $this->plugin->getLogger()->info("[chocbar] Despawned NPC '$npcId' for $name");
            }
        } else {
            // Despawn all NPCs for the player
            foreach ($this->spawnedNpcs[$name] ?? [] as $id => $npc) {
                if (!$npc->isClosed()) {
                    $npc->close();
                }
                $this->plugin->getLogger()->info("[chocbar] Despawned NPC '$id' for $name");
            }
            unset($this->spawnedNpcs[$name]);
        }
    }

    public function loadSkin(string $name): ?Skin {
        $path = $this->plugin->getDataFolder() . "skins/" . $name . ".png";

        if (!file_exists($path)) {
            $this->plugin->getLogger()->warning("Skin file not found at $path");
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
