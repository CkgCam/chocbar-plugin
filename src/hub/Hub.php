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
use ckgcam\chocbar\HotbarMenu\HotbarMenuManager;

class Hub {

    private Main $plugin;
    private BossBarManager $bossBarManager;

    private NpcSystem $npcSystem;

    private HotbarMenuManager $hotbarMenuManager;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }
    private function Logger(String $message): void
    {
        $this->plugin->getLogger()->info(TextFormat::YELLOW."[Hub]" . TextFormat::GREEN . "|" . TextFormat::WHITE . "[" . $message . "]");
    }



    public function enable(): void {

        //Get Needed Managers
        if($this->plugin->getScript("NpcSystem") != null)
        {
            $this->npcSystem = $this->plugin->getScript("NpcSystem");
        }

        if($this->plugin->getScript("BossBarManager") != null)
        {
            $this->bossBarManager = $this->plugin->getScript("BossBarManager");
        }

        if($this->plugin->getScript("HotbarMenuManager") != null)
        {
            $this->hotbarMenuManager = $this->plugin->getScript("HotbarMenuManager");
        }

        $this->plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            foreach ($this->plugin->getServer()->getWorldManager()->getWorlds() as $world) {
                $world->setTime(6000);
                $world->stopTime();
            }
        }), 100);

        $this->Logger("chocbar Hub Manager loaded!");
    }

    public function WhenPlayerJoins(Player $player): void {
        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName("hub");

        if ($world !== null) {
            $player->teleport($world->getSpawnLocation());

            $this->bossBarManager->showBossBar($player, "§bChocbar Hub | §7/menu for more");

            $position = new Vector3(0.52, 30, -37.44); // Replace with your actual coordinates
            $this->plugin->getNpcSystem()?->spawnHubNPC($player, $world, $position, "Survival Mode", "survival");
            $position = new Vector3(5.5, 30, -36.5); // Replace with your actual coordinates
            $this->plugin->getNpcSystem()?->spawnHubNPC($player, $world, $position, "Sky Block", "skyblock");
        } else {
            $this->plugin->getLogger()->warning("Hub world is not loaded!");
        }


    }

    public function onPlayerQuitEvent(Player $player): void {
        $this->plugin->getNpcSystem()?->despawnHubNPC($player);
    }





}
