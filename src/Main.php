<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use ckgcam\chocbar\HotbarMenu\HotbarMenuManager;
use ckgcam\chocbar\HotbarMenu\InventoryListener;
use ckgcam\chocbar\forms\FormsManager;
use ckgcam\chocbar\world\WorldManager;
use ckgcam\chocbar\survival\Survival;
use ckgcam\chocbar\bossbar\BossBarManager;
use ckgcam\chocbar\EventListener;
use ckgcam\chocbar\hub\Hub;

class Main extends PluginBase {

    private HotbarMenuManager $hotbarManager;
    private FormsManager $formsManager;
    private BossBarManager $bossBarManager;
    private WorldManager $worldManager;
    private ?Survival $survival = null;
    private ?Hub $hub = null;
    private string $servertype;

    private bool $blockTickingDisabled = false;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->servertype = strtolower($this->getConfig()->get("server-type"));

        // Register event listeners
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new InventoryListener($this), $this);

        // Initialize managers
        $this->hotbarManager = new HotbarMenuManager($this);
        $this->formsManager = new FormsManager($this);
        $this->bossBarManager = new BossBarManager($this);
        $this->worldManager = new WorldManager($this);

        $this->formsManager->enable();
        $this->hotbarManager->enable();

        // Load survival logic if server type is survival
        if ($this->servertype === "survival") {
            $this->survival = new Survival($this);
            $this->survival->setBossBarManager($this->bossBarManager);
            $this->blockTickingDisabled = false;
            $this->survival->enable();
        }

        // Load hub logic if server type is hub
        if ($this->servertype === "hub") {
            $this->hub = new Hub($this);
            $this->hub->setBossBarManager($this->bossBarManager);
            $this->blockTickingDisabled = true;
            $this->hub->enable();
        }

        $this->getLogger()->info(TextFormat::GREEN . "chocbar lib loaded!");
    }

    public function getSurvival(): ?Survival {
        return $this->survival;
    }

    public function getHub(): ?Hub {
        return $this->hub;
    }

    public function getServerType(): string {
        return $this->servertype;
    }

    public function isBlockTickingDisabled(): bool {
        return $this->blockTickingDisabled;
    }

    public function onDisable(): void {
        $this->getLogger()->info(TextFormat::RED . "chocbar lib shutting down!");
    }

    public function getHotbarMenuManager(): HotbarMenuManager {
        return $this->hotbarManager;
    }

    public function executeHotbarActions(Player $player, string $ID): void {
        switch ($ID) {
            case "openNaviForm":
                $this->formsManager->openNaviForm($player);
                break;
        }
    }

    public function DetachHud(Player $player): void {
        $this->hotbarManager->DetachHud($player);
    }

    public function onNaviFormClosed(Player $player, ?array $data): void {
        // Your custom logic here
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Use this command in-game.");
            return true;
        }

        switch ($cmd->getName()) {
            case "tpworld":
                $this->worldManager->handleCommand($sender, $cmd, $label, $args);
                return true;
            case "admin":
                $this->formsManager->AdminMenu($sender);
                return true;
            case "menu":
                $this->openGameModeMenu($sender);
                return true;
        }

        return false;
    }

    public function openGameModeMenu(Player $player): void {
        if ($this->servertype === "survival") {
            $this->formsManager->survivalmenu($player);
        }
    }
}
