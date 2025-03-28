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

    public function spawnHubNPC(Player $player, World $world, Vector3 $position): void {
        $name = $player->getName();

        if (isset($this->spawnedFor[$name])) {
            $this->plugin->getLogger()->info("[chocbar] NPC already spawned for $name, skipping.");
            return;
        }

        $this->plugin->getLogger()->info("[chocbar] Spawning NPC for $name");

        $skin = $player->getSkin();
        $location = new Location(
            $position->getX(),
            $position->getY(),
            $position->getZ(),
            $world,
            0.0, 0.0
        );

        $npc = new Human($location, $skin);
        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible();

        // ❗ DON'T call addEntity() + spawnTo(), just spawnTo()
        $npc->spawnTo($player);

        $this->spawnedFor[$name] = true;
    }

}
