<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use ckgcam\chocbar\npc\npc_survival;
use ckgcam\chocbar\npc\CustomNPC;
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
use ckgcam\chocbar\survival\Survival;
use ckgcam\chocbar\world\WorldManager;
use ckgcam\chocbar\EventListener;

use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

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

    private function Logger(String $message): void
    {
        $this->getLogger()->info(TextFormat::DARK_PURPLE."[Main]" . TextFormat::GREEN . "|" . TextFormat::WHITE . "[" . $message . "]");
    }


    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->saveResource("skins/test.png");
        $this->servertype = strtolower($this->getConfig()->get("server-type", "hub"));

        // Register core event listener
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->Logger("Registered EventListener");

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
            $this->blockTickingDisabled = true;
        }
        $this->Logger("chocbar lib loaded!");
    }

    public function onDisable(): void {
        $this->Logger("chocbar lib shutting down!");
    }

    // Getters
    public function getSurvival(): ?Survival {
        return $this->survival;
    }

    public function getScript(String $type)
    {
        $this->Logger("Script is asking for script:  " . $type);

        switch ($type) {
            case "NpcSystem":
                return $this->npcSystem;
                case "hub":
                    return $this->hub;
                    case "BossBarManager":
                        return $this->bossBarManager;
                        case "HotbarMenuManager":
                            return $this->hotbarManager;
                    default:
                        return null;
        }
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

    public function onNpcTapped(Player $player, string $npcId): void {
        $this->Logger("Received NPC Tap");

        switch ($npcId) {
            case "survival":
                $this->Logger("Opening Survival Join Form...");
                break;
                    default:
                        $this->Logger("This NPC Tap Is Not Binded");
        }

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
