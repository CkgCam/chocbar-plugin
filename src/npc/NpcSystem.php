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

    private ?Human $hubNpc = null;

    public function spawnHubNPC(World $world, Vector3 $position): void {
        if ($this->hubNpc !== null && !$this->hubNpc->isClosed()) {
            $this->plugin->getLogger()->info("Hub NPC already exists.");
            return;
        }

        $this->plugin->getLogger()->info("Spawning new hub NPC...");

        $skin = new Skin("Standard_Custom", str_repeat("\x00", 8192));
        $location = new Location($position->getX(), $position->getY(), $position->getZ(), $world, 0, 0);
        $npc = new Human($location, $skin);

        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);

        $world->addEntity($npc); // spawns for all players nearby
        $this->hubNpc = $npc;
    }



}
