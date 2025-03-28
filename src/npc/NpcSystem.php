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

        $skin = new Skin("Standard_Custom", str_repeat("\x00", 8192)); // default Steve skin
        $location = new Location($position->getX(), $position->getY(), $position->getZ(), $world, 0, 0);
        $npc = new Human($location, $skin);

        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);

        $npc->spawnTo($player); // only show to that player

        $this->spawnedNpcs[$name] = $npc;
    }



}
