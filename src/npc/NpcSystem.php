<?php
declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use ckgcam\chocbar\Main;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\entity\Location;

class NpcSystem {
    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position): void {
        $this->plugin->getLogger()->info("Spawning NPC for " . $player->getName());

        // Use player's skin to ensure it's visible
        $skin = $player->getSkin();

        // Build the Location object
        $location = new Location(
            $position->getX(),
            $position->getY(),
            $position->getZ(),
            $world,
            0.0, // yaw
            0.0  // pitch
        );

        // Create the NPC
        $npc = new Human($location, $skin);
        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);
        $npc->setImmobile(true); // Optional: stop it from walking off

        // Add to world and show to player
        $world->addEntity($npc);
        $npc->spawnTo($player);
    }
}
