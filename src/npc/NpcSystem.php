<?php
declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use ckgcam\chocbar\Main;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\World;

class NpcSystem
{
    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function spawnHubNPC(Player $player, World $world, Vector3 $position): void {
        $this->plugin->getLogger()->info("Spawning NPC for " . $player->getName());

        // Using a blank skin - optional: load from file instead
        $skin = new Skin("Standard_Custom", str_repeat("\x00", 8192)); // fake 64x32 skin

        $nbt = Human::createBaseNBT($position);
        $npc = new Human($world, $nbt, $skin);
        $npc->setNameTag("§aHello, Steve!");
        $npc->setNameTagVisible(true);
        $npc->setNameTagAlwaysVisible(true);
        $npc->spawnTo($player);
    }
}
