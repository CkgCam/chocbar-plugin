<?php

declare(strict_types=1);

namespace ckgcam\chocbar\transfer;

use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;

class Transfer {

    /**
     * Transfer type:
     * 0 = Internal IPs (LAN)
     * 1 = External IPs (e.g. chocbar.net)
     * 2 = Waterdog Proxy (Downstream name only)
     */
    public int $transfertype = 0;

    public function send(Player $player, string $serverID): void {
        switch ($this->transfertype) {
            case 0:
                $this->internal($player, $serverID);
                break;

            case 1:
                $this->normal($player, $serverID);
                break;

            case 2:
                $this->waterDog($player, $serverID);
                break;

            default:
                $player->sendMessage("Unknown transfer type.");
        }
    }

    private function internal(Player $player, string $serverID): void {
        switch ($serverID) {
            case "survival":
                $this->transferPlayer($player, "192.168.1.11", 19136);
                break;
            default:
                $player->sendMessage("Unknown internal server ID.");
        }
    }

    private function normal(Player $player, string $serverID): void {
        switch ($serverID) {
            case "survival":
                $this->transferPlayer($player, "chocbar.net", 19136);
                break;
            default:
                $player->sendMessage("Unknown external server ID.");
        }
    }

    private function waterDog(Player $player, string $serverID): void {
        // Waterdog-specific transfer usually requires a plugin messaging system.
        // This is only possible if you're using a WaterdogPE-compatible proxy
        // and a plugin that communicates with it (e.g., via Redis or plugin messaging channel).
        $player->transfer($serverID); // Only works if your PMMP is proxy-aware
    }

    private function transferPlayer(Player $player, string $ip, int $port): void {
        $pk = new TransferPacket();
        $pk->address = $ip;
        $pk->port = $port;

        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
