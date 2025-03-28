<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ckgcam\chocbar\bossbar\BossBarManager;
use ckgcam\chocbar\forms\FormsManager;
use ckgcam\chocbar\HotbarMenu\HotbarMenuManager;
use ckgcam\chocbar\HotbarMenu\InventoryListener;
use ckgcam\chocbar\hub\Hub;
use ckgcam\chocbar\npc\NpcSystem;
use ckgcam\chocbar\ProtocolBypassListener;
use ckgcam\chocbar\survival\Survival;
use ckgcam\chocbar\world\WorldManager;

class Main extends PluginBase {

    private HotbarMenuManager $hotbarManager;
    private FormsManager $formsManager;
    private BossBarManager $bossBarManager;
    private WorldManager $worldManager;
    private ?NpcSystem $npcSystem = null;
    private ?Survival $survival = null;
    private ?Hub $hub = null;
    private string $servertype;
    private bool $blockTickingDisabled = false;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->servertype = strtolower($this->getConfig()->get("server-type", "hub"));

        // Register core event listener
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getLogger()->info("Registered EventListener ✅");

        // Initialize core managers
        $this->hotbarManager = new HotbarMenuManager($this);
        $this->formsManager = new FormsManager($this);
        $this->bossBarManager = new BossBarManager($this);
        $this->worldManager = new WorldManager($this);
        $this->npcSystem = new NpcSystem($this);

        $this->formsManager->enable();
        $this->hotbarManager->enable();

        // Load specific game mode logic
        if ($this->servertype === "survival") {
            $this->survival = new Survival($this);
            $this->survival->setBossBarManager($this->bossBarManager);
            $this->survival->enable();
            $this->blockTickingDisabled = false;
        } elseif ($this->servertype === "hub") {
            $this->hub = new Hub($this);
            $this->hub->setBossBarManager($this->bossBarManager);
            $this->hub->enable();
            $this->blockTickingDisabled = true;
        }

        $this->getLogger()->info(TextFormat::GREEN . "chocbar lib loaded!");
    }

    public function onDisable(): void {
        $this->getLogger()->info(TextFormat::RED . "chocbar lib shutting down!");
    }

    // Getters
    public function getSurvival(): ?Survival {
        return $this->survival;
    }

    public function getHub(): ?Hub {
        return $this->hub;
    }

    public function getNpcSystem(): ?NpcSystem {
        return $this->npcSystem;
    }

    public function getHotbarMenuManager(): HotbarMenuManager {
        return $this->hotbarManager;
    }

    public function getServerType(): string {
        return $this->servertype;
    }

    public function isBlockTickingDisabled(): bool {
        return $this->blockTickingDisabled;
    }

    // Commands
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Use this command in-game.");
            return true;
        }

        return match ($cmd->getName()) {
            "tpworld" => $this->worldManager->handleCommand($sender, $cmd, $label, $args),
            "admin" => $this->formsManager->AdminMenu($sender),
            "menu" => $this->openGameModeMenu($sender),
            default => false,
        };
    }

    public function openGameModeMenu(Player $player): void {
        if ($this->servertype === "survival") {
            $this->formsManager->survivalmenu($player);
        }
    }

    public function executeHotbarActions(Player $player, string $ID): void {
        if ($ID === "openNaviForm") {
            $this->formsManager->openNaviForm($player);
        }
    }

    public function DetachHud(Player $player): void {
        $this->hotbarManager->DetachHud($player);
    }

    public function onNaviFormClosed(Player $player, ?array $data): void {
        // Add your custom form response logic here
    }
}
