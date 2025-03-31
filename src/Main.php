<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use ckgcam\chocbar\EventListener;
use ckgcam\chocbar\npc\npc_survival;
use ckgcam\chocbar\npc\CustomNPC;
use ckgcam\chocbar\transfer\Transfer;
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

use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class Main extends PluginBase {

    private EventListener $eventListener;
    private HotbarMenuManager $hotbarManager;
    private FormsManager $formsManager;
    private BossBarManager $bossBarManager;
    private WorldManager $worldManager;
    private Transfer $transfer;
    private ?NpcSystem $npcSystem = null;
    private ?Survival $survival = null;
    private ?Hub $hub = null;
    private string $servertype;
    private bool $blockTickingDisabled = false;

    private function Logger(String $message): void
    {
        $this->getLogger()->info(TextFormat::DARK_PURPLE."[Main]" . TextFormat::GREEN . " > " . TextFormat::WHITE . "[" . $message . "]");
    }


    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->saveResource("skins/test.png");
        $this->servertype = strtolower($this->getConfig()->get("server-type", "hub"));

        $icons = ["default.png"];
        foreach ($icons as $icon) {
            $this->saveResource("forms/icons/" . $icon);
        }

        // Register core event listener
        $this->eventListener = new EventListener($this);
        $this->getServer()->getPluginManager()->registerEvents($this->eventListener, $this);
        $this->Logger("Registered EventListener");


        // Initialize core managers
        $this->hotbarManager = new HotbarMenuManager($this);
        $this->formsManager = new FormsManager($this);
        $this->bossBarManager = new BossBarManager($this);
        $this->worldManager = new WorldManager($this);
        $this->transfer =  new Transfer($this);
        $this->npcSystem = new NpcSystem($this);

        // Load specific game mode logic
        if ($this->servertype === "survival") {
            $this->survival = new Survival($this);
            $this->survival->setBossBarManager($this->bossBarManager);
            $this->survival->enable();
            $this->blockTickingDisabled = false;
        } elseif ($this->servertype === "hub") {
            $this->hub = new Hub($this);
            $this->hub->enable();
            $this->blockTickingDisabled = true;
        }

        $this->eventListener->enable();
        $this->formsManager->enable();
        $this->hotbarManager->enable();

        $this->Logger("chocbar lib loaded!");
    }

    public function onDisable(): void {
        $this->Logger("chocbar lib shutting down!");
    }

    //return instances of the scripts based on there names
    public function getScript(string $type): mixed
    {
        return match ($type) {
            "NpcSystem" => $this->npcSystem,
            "hub" => $this->hub,
            "BossBarManager" => $this->bossBarManager,
            "HotbarMenuManager" => $this->hotbarManager,
            "FormsManager" => $this->formsManager,
            "Transfer" => $this->transfer,
            default => null,
        };
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
            //"admin" => $this->formsManager->AdminMenu($sender),
            //"menu" => $this->openGameModeMenu($sender),
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
