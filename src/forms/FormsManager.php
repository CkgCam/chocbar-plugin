<?php

declare(strict_types=1);

namespace ckgcam\chocbar\forms;

use ckgcam\chocbar\transfer\Transfer;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use ckgcam\chocbar\Main;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use ckgcam\chocbar\HotbarMenu\HotbarMenuManager;

class FormsManager {

    private Main $plugin; // ✅ Correct type hint

    private HotbarMenuManager $hotbarMenuManager;

    private Transfer $transfer;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    private function logger(string $message): void
    {
        $this->plugin->getLogger()->info(
            TextFormat::YELLOW . "[FormManager]" .
            TextFormat::GREEN . " > " .
            TextFormat::WHITE . "[{$message}]"
        );
    }

    public function enable(): void
    {
        //$this->hotbarMenuManager = $this->plugin->getHotbarMenuManager();
        $this->hotbarMenuManage = $this->plugin->getScript("HotbarMenuManager");
        $this->transfer = $this->plugin->getScript("Transfer");
        $this->logger("chocbar Forms Manager loaded!");
    }
    public function openNaviForm(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, ?int $data)
        {
            if ($data === null) return; // Player closed the form
            switch ($data) {
                case 0:
                    $player->sendMessage(TextFormat::GREEN . "Teleporting to survival...");
                    $this->transfer->transfer($player, "survival");
                    break;
                    case 1:
                        $player->sendMessage(TextFormat::GREEN . "Teleporting to skyblock...");
                        break;
                        default:
                            $player->sendMessage(TextFormat::RED . "Unknown Teleportion");
            }

        });

        $form->setTitle(TextFormat::RED . TextFormat::BOLD . "Navi Menu blaaaa");


        //all the below is quite usless becuse we
        $iconPath = $this->plugin->getDataFolder() . "forms/icons/default.png";

        if (!file_exists($iconPath)) {
            $this->logger("Icon not found at: $iconPath");
            $size = filesize($iconPath);
            $this->$this->logger("Icon file size: {$size} bytes");
        } else {
            $this->logger("Icon loaded from: $iconPath");
        }

        $base64 = "data:image/png;base64," . base64_encode(file_get_contents($iconPath));
        $this->logger("Base64 preview: " . substr($base64, 0, 60));


        $form->addButton(TextFormat::BOLD.TextFormat::DARK_RED."Survival Mode ".TextFormat::RESET . "0 Players", 1, "https://raw.githubusercontent.com/CkgCam/formicontest/refs/heads/main/default.png");
        $form->addButton("Skyblock",1, "https://raw.githubusercontent.com/CkgCam/formicontest/refs/heads/main/default.png");


        //Prob dont need this now but ill keep for now
        $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use($player, $form): void {
            $player->sendForm($form);
        }), 1); // Delay by 5 ticks maby fix this bs icon loading
    }

    public function AdminMenu(Player $player): void
    {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) {
                $player->sendMessage(TextFormat::RED . "You closed the menu.");
                return;
            }

            switch ($data) {
                case 0:
                    $player->sendMessage(TextFormat::GREEN . "You selected Option 1!");
                    break;
                case 1:
                    $this->Admin_InvenHudSettings($player);
                    break;
                case 2:
                    $player->sendMessage(TextFormat::BLUE . "You selected Option 3!");
                    break;
            }
        });

        // Form Title
        $form->setTitle("Admin Menu");

        // Add buttons
        $form->addButton("Transfer Server", 0); // First button (index 0)
        $form->addButton("Inventory Hud Settings", 0); // Second button (index 1)
        // Send the form
        $player->sendForm($form);
    }

    public function Admin_InvenHudSettings(Player $player): void {
        $form = new CustomForm(function (Player $player, ?array $data) {
            if ($data === null) {
                $player->sendMessage(TextFormat::RED . "Form cancelled.");
                return;
            }

            $enabled = $data[2] ?? true;

            if (!$enabled) {
                $this->plugin->DetachHud($player);
                $player->sendMessage(TextFormat::RED . "Inventory HUD disabled.");
            } else {
                //need to also pass in other stuff i just left the for future me
                //$this->plugin->AttachHud($player);
                $player->sendMessage(TextFormat::GREEN . "Inventory HUD enabled.");
            }
        });

        $form->setTitle("Inventory HUD Settings");
        if($this->hotbarMenuManager->isPlayerAttached($player))
        {
            $form->addLabel("You currently have " . $this->hotbarMenuManager->getPlayersAttachedHudType($player) . " hud attached.");
        }
        else
        {
            $form->addLabel("No inventory HUD attached.");
        }
        $form->addLabel("Enable/Disable HUD");
        $form->addToggle("Enabled", true);
        $player->sendForm($form);
    }
    
    public function survivalmenu(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
          if ($data === null) {
             $player->sendMessage(TextFormat::RED . "You closed the menu.");
             return;
            }

            switch ($data) {
                case 0:
                    $player->sendMessage(TextFormat::GREEN . "You selected Option 1!");
                    break;
                case 1:
                    $player->sendMessage(TextFormat::GREEN . "You selected Option 2!");
                    break;
                case 2:
                    $player->sendMessage(TextFormat::BLUE . "You selected Option 3!");
                    break;
            }
        });
        
        $form->setTitle("Menu");
        $form->addButton("Set Home", 0); // First button (index 0)
        $form->addButton("Protect", 0);
        $form->addButton("Shopping", 0);
        $form->addButton("Loot Crate", 0);
        $form->addButton("Earn Money", 0);
        $player->sendForm($form);
    }


}
