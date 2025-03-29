<?php

declare(strict_types=1);

namespace ckgcam\chocbar\hub;

use ckgcam\chocbar\Main;
use ckgcam\chocbar\bossbar\BossBarManager;
use ckgcam\chocbar\npc\NpcSystem;
use ckgcam\chocbar\HotbarMenu\HotbarMenuManager;
use ckgcam\chocbar\HotbarMenu\Hotbars\HubHotbar;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class Hub {

    private Main $plugin;
    private ?BossBarManager $bossBarManager = null;
    private ?NpcSystem $npcSystem = null;
    private ?HotbarMenuManager $hotbarMenuManager = null;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    private function logger(string $message): void {
        $this->plugin->getLogger()->info(TextFormat::YELLOW . "[Hub]" . TextFormat::GREEN . "|" . TextFormat::WHITE . "[{$message}]");
    }

    public function enable(): void {
        $this->npcSystem = $this->plugin->getNpcSystem();
        $this->bossBarManager = $this->plugin->getBossBarManager();
        $this->hotbarMenuManager = $this->plugin->getHotbarMenuManager();

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

            if ($this->npcSystem !== null) {
                $this->npcSystem->spawnHubNPC($player, $world, new Vector3(0.52, 30, -37.44), "Survival Mode", "survival");
                $this->npcSystem->spawnHubNPC($player, $world, new Vector3(5.5, 30, -36.5), "Sky Block", "skyblock");
            }
        } else {
            $this->logger("Hub world is not loaded!");
        }

        if ($this->hotbarMenuManager !== null) {
            $this->hotbarMenuManager->ApplyHotbar($player, new HubHotbar());
        }
    }

    public function onPlayerQuit(Player $player): void {
        if ($this->npcSystem !== null) {
            $this->npcSystem->despawnHubNPC($player);
        }
    }
}
