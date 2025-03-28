<?php

declare(strict_types=1);

namespace ckgcam\chocbar\hub;

use ckgcam\chocbar\bossbar\BossBarManager;
use ckgcam\chocbar\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\ClosureTask;
use pocketmine\math\Vector3;
use ckgcam\chocbar\npc\NpcSystem;

class Hub implements Listener {

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

    public function onPlayerJoin(Player $player): void {
        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName("hub");

        if ($world !== null) {
            // Optionally teleport player to hub spawn
            $player->teleport($world->getSpawnLocation());

            // Set the boss bar
            $this->bossBarManager->showBossBar($player, "§bChocbar Hub | §7/menu for more");

            // Spawn NPC at specific coords
            $position = new Vector3(3, 32, -2); // Replace with actual hub coords
            $this->plugin->getNpcSystem()?->spawnHubNPC($player, $world, $position);

        } else {
            $this->plugin->getLogger()->warning("Hub world is not loaded!");
        }
    }



}
