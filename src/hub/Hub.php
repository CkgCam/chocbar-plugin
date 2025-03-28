<?php

declare(strict_types=1);

namespace ckgcam\chocbar\hub;

use ckgcam\chocbar\bossbar\BossBarManager;
use ckgcam\chocbar\Main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\ClosureTask;
use pocketmine\math\Vector3;
use ckgcam\chocbar\npc\NpcSystem;

class Hub {

    private Main $plugin;
    private BossBarManager $bossBarManager;

    private NpcSystem $npcSystem;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;

        // Register this class as an event listener
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function setBossBarManager(BossBarManager $bossBarManager): void {
        $this->bossBarManager = $bossBarManager;
    }

    public function setNpcSystem(NpcSystem $npcSystem): void {
        $this->npcSystem = $npcSystem;
    }

    public function enable(): void {
        $this->plugin->getLogger()->info(TextFormat::GREEN . "chocbar Hub Manager loaded!");

        $this->plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            foreach ($this->plugin->getServer()->getWorldManager()->getWorlds() as $world) {
                $world->setTime(6000);
                $world->stopTime();
            }
        }), 100);

        $this->plugin->getLogger()->info(TextFormat::GREEN . "Time locked to midday in Hub worlds.");
    }

    public function WhenPlayerJoins(Player $player): void {
        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName("hub");

        if ($world !== null) {
            $player->teleport($world->getSpawnLocation());

            $this->bossBarManager->showBossBar($player, "§bChocbar Hub | §7/menu for more");

            $position = new Vector3(25, 65, 45); // Replace with your actual coordinates
            $this->plugin->getNpcSystem()?->spawnHubNPC($player, $world, $position);
        } else {
            $this->plugin->getLogger()->warning("Hub world is not loaded!");
        }
    }




}
