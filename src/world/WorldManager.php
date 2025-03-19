<?php

namespace ckgcam\chocbar\world;

use ckgcam\chocbar\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class WorldManager {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function handleCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        if ($cmd->getName() === "tpworld") {
            if (!$sender instanceof Player) {
                $sender->sendMessage("Use this command in-game.");
                return true;
            }

            if (count($args) !== 1) {
                $sender->sendMessage("Usage: /tpworld <worldname>");
                return true;
            }

            $worldName = $args[0];
            $worldManager = $this->plugin->getServer()->getWorldManager();

            $world = $worldManager->getWorldByName($worldName);
            if ($world === null) {
                $worldManager->loadWorld($worldName);
                $world = $worldManager->getWorldByName($worldName);
            }

            if ($world === null) {
                $sender->sendMessage("World '" . $worldName . "' does not exist.");
                return true;
            }

            $sender->teleport($world->getSafeSpawn());
            $sender->sendMessage("Teleported to world '" . $worldName . "'.");

            return true;
        }

        return false;
    }
}