<?php
declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use ckgcam\chocbar\Main;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\entity\Location;
use pocketmine\world\World;

class NpcSystem {
    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position): void {
        $this->plugin->getLogger()->info("Spawning NPC for " . $player->getName());

        $skin = $player->getSkin(); // Use player's skin for now
        $location = new Location(
            $position->getX(),
            $position->getY(),
            $position->getZ(),
            $world,
            0.0, // yaw
            0.0  // pitch
        );

        $npc = new Human($location, $skin);
        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);

        $npc->spawnTo($player); // Only this — don't use addEntity()
    }
}
