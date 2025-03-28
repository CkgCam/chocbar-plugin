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

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position): void {
        $this->plugin->getLogger()->info("[chocbar] Spawning NPC for " . $player->getName());

        // Grab player's skin for now (just for testing)
        $skin = $player->getSkin();

        // Create Location
        $location = new Location(
            $position->getX(),
            $position->getY(),
            $position->getZ(),
            $world,
            0.0, // yaw
            0.0  // pitch
        );

        // Create NPC entity
        $npc = new Human($location, $skin);
        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible();

        // Add entity to world
        $world->addEntity($npc);

        // Make visible to player
        $npc->spawnTo($player);
    }
}
