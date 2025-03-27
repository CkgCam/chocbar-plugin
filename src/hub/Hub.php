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

class Hub implements Listener {

    private Main $plugin;
    private BossBarManager $bossBarManager;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;

        // Register this class as an event listener
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function setBossBarManager(BossBarManager $bossBarManager): void {
        $this->bossBarManager = $bossBarManager;
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

    public function onPlayerJoin(PlayerJoinEvent $player): void {
        $this->plugin->getLogger()->info("Hub: Player joined: " . $player->getName());

        $player = $player->getPlayer();

        $this->bossBarManager->showBossBar($player, "Chocbar Hub | /menu for more");

        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName("hub");
        if ($world !== null) {
            $pos = new Vector3(10, 65, 10);
            $npcSystem = $this->plugin->getNpcSystem();
            if ($npcSystem !== null) {
                $npcSystem->spawnHubNPC($player, $world, $pos);
            }
        }
    }

}
