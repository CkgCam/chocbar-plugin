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

class NpcSystem {

    private Main $plugin;
    public array $spawnedFor = []; // Track players to prevent duplicate spawning

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function spawnHubNPC(Player $player): void {
        $this->plugin->getLogger()->info("[chocbar] Spawning NPC for " . $player->getName());

        $skin = $player->getSkin(); // Use player's skin, or load a custom one
        $world = $player->getWorld();

        $position = new Vector3(24.5, 28, 43.5); // change these coords as needed

        $location = new Location(
            $position->getX(),
            $position->getY(),
            $position->getZ(),
            $world,
            0.0, 0.0 // yaw & pitch
        );

        $npc = new Human($location, $skin);
        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible();

        // Spawn to all players or just one
        $npc->spawnToAll();
    }


}
