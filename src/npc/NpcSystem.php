<?php

declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use ckgcam\chocbar\Main;
use ckgcam\chocbar\npc\NPC;
use ckgcam\chocbar\npc\SkinLoader;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

class NpcSystem {

    private Main $plugin;
    private SkinLoader $skinLoader;

    /**
     * @var array<string, array<string, NPC>>
     * Format: $spawnedNpcs[playerName][npcId] = NPC instance
     */
    private array $spawnedNpcs = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->skinLoader = new SkinLoader($plugin);
    }

    private function Logger(string $message): void {
        $this->plugin->getLogger()->info(TextFormat::YELLOW . "[NPC System]" . TextFormat::GREEN ." > " . TextFormat::WHITE ."[". $message . "]");
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position, string $nametag, string $npcId): void {
        $name = $player->getName();

        $this->Logger("Spawning NPC For $name at {$world->getDisplayName()} @ {$position->getX()}, {$position->getY()}, {$position->getZ()} | Nametag: $nametag | ID: $npcId");

        if (isset($this->spawnedNpcs[$name][$npcId]) && !$this->spawnedNpcs[$name][$npcId]->isClosed()) {
            $this->Logger("NPC '$npcId' already spawned for $name");
            return;
        }

        $this->Logger("Loading skin with SkinLoader...");
        $skin = $this->skinLoader->load($npcId) ?? new Skin("fallback", str_repeat("\x00", 8192));
        $location = new Location($position->getX(), $position->getY(), $position->getZ(), $world, 0.0, 0.0);

        $this->Logger("Instantiating NPC...");
        $npc = new NPC($location, $skin);
        $npc->setNpcId($npcId);

        $npc->setNameTag($nametag);
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);

        $this->Logger("Spawning NPC to player...");
        $npc->spawnTo($player);
        $npc->enableParticles(true);

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

            if ($npc instanceof NPC) {
                if (!$npc->isClosed()) {
                    $npc->flagForDespawn();
                    $this->Logger("Despawned NPC '$npcId' for $name");
                } else {
                    $this->Logger("NPC '$npcId' was already closed for $name");
                }
                unset($this->spawnedNpcs[$name][$npcId]);
            } else {
                $this->Logger("NPC '$npcId' not found for $name");
            }

            if (empty($this->spawnedNpcs[$name])) {
                unset($this->spawnedNpcs[$name]);
            }

        } else {
            foreach ($this->spawnedNpcs[$name] as $id => $npc) {
                if ($npc instanceof NPC && !$npc->isClosed()) {
                    $npc->flagForDespawn();
                    $this->Logger("Despawned NPC '$id' for $name");
                }
            }

            unset($this->spawnedNpcs[$name]);
        }
    }
}
