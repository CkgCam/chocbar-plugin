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
use pocketmine\world\Position;


class NpcSystem
{
    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position): void {
        $this->plugin->getLogger()->info("Spawning NPC for " . $player->getName());

        // Fake Steve skin (replace later with real one)
        $skin = new Skin("Standard_Custom", str_repeat("\x00", 8192));

        // Create the entity with proper Location
        $location = new Location($position->getX(), $position->getY(), $position->getZ(), $world, 0, 0);

        $npc = new Human($location, $skin);
        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);

        // Spawn only to the player for now
        $npc->spawnTo($player);
    }
}
