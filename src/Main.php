<?php

declare(strict_types=1);

namespace ckgcam\chocbar;

use ckgcam\chocbar\EventListener;
use ckgcam\chocbar\npc\npc_survival;
use ckgcam\chocbar\npc\CustomNPC;
use ckgcam\chocbar\screentext\ScreenTextManager;
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

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class Main extends PluginBase
{

    private EventListener $eventListener;
    private HotbarMenuManager $hotbarManager;
    private FormsManager $formsManager;
    private BossBarManager $bossBarManager;
    private WorldManager $worldManager;
    private Transfer $transfer;
    private ?NpcSystem $npcSystem = null;
    private ?Survival $survival = null;
    private ?Hub $hub = null;

    private ScreenTextManager $screenTextManager;
    private string $servertype;
    private bool $blockTickingDisabled = false;

    private DataConnector $db;

    private function Logger(string $message): void
    {
        $this->getLogger()->info(TextFormat::DARK_PURPLE . "[Main]" . TextFormat::GREEN . " > " . TextFormat::WHITE . "[" . $message . "]");
    }



    public function onEnable(): void
    {

        $this->saveDefaultConfig();
        $this->saveResource("skins/test.png");
        $this->servertype = strtolower($this->getConfig()->get("server-type", "hub"));

        $this->saveResource("skins/survival.png");
        $this->saveResource("geo/surviva.geol.json");

        // Register core event listener
        $this->eventListener = new EventListener($this);
        $this->getServer()->getPluginManager()->registerEvents($this->eventListener, $this);
        $this->Logger("Registered EventListener");


        // Initialize core managers
        $this->hotbarManager = new HotbarMenuManager($this);
        $this->formsManager = new FormsManager($this);
        $this->bossBarManager = new BossBarManager($this);
        $this->worldManager = new WorldManager($this);
        $this->transfer = new Transfer($this);
        $this->npcSystem = new NpcSystem($this);
        $this->screenTextManager = new ScreenTextManager($this);
        $this->InitalizeServerSpeficManagers();

        $this->eventListener->enable();
        $this->formsManager->enable();
        $this->hotbarManager->enable();

        $this->Logger("chocbar lib loaded!");

            $this->saveResource("mysql.sql");
            $config = [
                "type" => "mysql",
                "host" => "192.168.1.70",
                "user" => "your_user",
                "password" => "your_pass",
                "database" => "your_db",
                "port" => 3306
            ];
            $this->db = libasynql::create($this, $config, [
                "mysql" => "mysql.sql"
            ]);
            $this->db->executeGeneric("init");
        }

    public function getDb(): DataConnector
    {
        return $this->db;
    }



    private function InitalizeServerSpeficManagers(): void {
        switch ($this->servertype) {
            case "hub":
                $this->hub = new Hub($this);
                $this->hub->enable();
                break;
            case "survival":
                $this->survival = new Survival($this);
                $this->survival->enable();
                break;
            default:
                $this->Logger("Unknown server type");

        }
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
            "ScreenTextManager" => $this->screenTextManager,
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
            //"tpworld" => $this->worldManager->handleCommand($sender, $cmd, $label, $args),
            //"admin" => $this->formsManager->AdminMenu($sender),
            //"menu" => $this->openGameModeMenu($sender),
            default => false,
        };
    }

    public function onInteract(Player $player, mixed $id): void
    {
        $this->Logger("New OnInteractc Event With ID: ".$id);
        switch ($id) {
            case "survival":
                $this->Logger("Opening Survival Join Form...");
                break;
                case "openNavi":
                    $this->formsManager->openNaviForm($player);
                    break;
            default:
                $this->Logger("This Interaction event (".$id.") does not exist.");
        }
    }
}
