<?php

declare(strict_types=1);

namespace ckgcam\chocbar\hub;

use ckgcam\chocbar\bossbar\BossBarManager;
use ckgcam\chocbar\Main;
use ckgcam\chocbar\screentext\ScreenTextManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\ClosureTask;
use pocketmine\math\Vector3;
use ckgcam\chocbar\npc\NpcSystem;
use ckgcam\chocbar\HotbarMenu\HotbarMenuManager;
use ckgcam\chocbar\HotbarMenu\Hotbars;
use pocketmine\Server;

class Hub {

    private Main $plugin;
    private ?BossBarManager $bossBarManager = null;
    private ?NpcSystem $npcSystem = null;
    private ?HotbarMenuManager $hotbarMenuManager = null;

    private ?ScreenTextManager $screenTextManager = null;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    private function logger(string $message): void {
        $this->plugin->getLogger()->info(TextFormat::YELLOW . "[Hub]" . TextFormat::GREEN . " > " . TextFormat::WHITE . "[{$message}]");
    }

    public function enable(): void {
        $this->npcSystem = $this->plugin->getScript("NpcSystem");
        $this->bossBarManager = $this->plugin->getScript("BossBarManager");
        $this->hotbarMenuManager = $this->plugin->getScript("HotbarMenuManager");
        $this->screenTextManager = $this->plugin->getScript("ScreenTextManager");

        $this->plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            foreach ($this->plugin->getServer()->getWorldManager()->getWorlds() as $world) {
                $world->setTime(6000);
                $world->stopTime();
            }
        }), 100);

        $this->logger("Hub Manager enabled!");
    }

    public function onPlayerJoin(Player $player): void {
        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName("hub");

        if ($world !== null) {
            $player->teleport($world->getSpawnLocation());

            if ($this->bossBarManager !== null) {
                $this->bossBarManager->showBossBar($player, "§bChocbar Hub | §7/menu for more");
            }

            if($this->screenTextManager !== null) {
                $this->screenTextManager->send($player,"Welcome to Chocbar!","Enjoy Your Stay", 0 , 20, 12, null);
            }

            if ($this->npcSystem !== null) {
                $this->npcSystem->spawnHubNPC($player, $world, new Vector3(0.52, 30, -37.44), "Survival Mode", "survival");
                $this->npcSystem->spawnHubNPC($player, $world, new Vector3(-4.52, 30, -36.77), "Prison", "prison");
                $this->npcSystem->spawnHubNPC($player, $world, new Vector3(5.5, 30, -36.5), "Sky Block", "skyblock");
            }
        } else {
            $this->logger("Hub world is not loaded!");
        }

        if ($this->hotbarMenuManager !== null) {
            $this->hotbarMenuManager->ApplyHotbar($player, Hotbars::$hub);
        }
    }

    public function onPlayerQuit(Player $player): void {
        if ($this->npcSystem !== null) {
            $this->npcSystem->despawnHubNPC($player);
        }
    }
}
