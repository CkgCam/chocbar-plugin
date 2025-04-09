<?php

declare(strict_types=1);

namespace ckgcam\chocbar\screentext;

use ckgcam\chocbar\Main;
use pocketmine\world\sound\Sound;
use pocketmine\world\sound\BellRingSound;

class ScreenTextManager {

    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function send(Player $player, string $title = "", string $subtitle = "", int $fadeIn = 10, int $stay = 40, int $fadeOut = 10, ?string $sound = null): void {
        if ($title !== "" || $subtitle !== "") {
            $player->sendTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
        }

        if ($sound !== null) {
            $player->getWorld()->addSound($player->getLocation(), new BellRingSound());
        }
    }
}

