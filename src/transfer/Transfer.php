<?php

declare(strict_types=1);

namespace ckgcam\chocbar\transfer;

use pocketmine\player\Player;

class Transfer
{
    //Types
    //0 = internal ips
    //1 = normal extranal ips
    //2 = waterdog proxy
    public int $transfertype = 0;
    public function Transfer(Player $player, String $ServerID)
    {
        switch ($this->transfertype) {
            case 0:
                $this->internal($player, $ServerID);
            break;
                case 1:
                    $this->normal($player, $ServerID);
                    break;
                    case 2:
                        $this->waterDog($player, $ServerID);
                        break;
                        default:


        }
    }

    private function internal(Player $player, String $ServerID)
    {
        switch ($ServerID)
        {
            case "survival":
                $this->transferPlayer($player, "192.168.1.11", 19136);
                break;
                default:
        }
    }

    private function normal(Player $player, String $ServerID)
    {
        switch ($ServerID)
        {
            case "survival":
                $this->transferPlayer($player, "chocbar.net", 19136);
                break;
            default:
        }
    }

    private function waterDog(Player $player, String $ServerID)
    {
        switch ($ServerID)
        {
            case "survival":
                //handle diff as no port is needed just the name of the waterdog downstream name
                break;
            default:
        }
    }

    public function transferPlayer(Player $player, string $ip, int $port): void {
        $pk = new TransferPacket();
        $pk->address = $ip;
        $pk->port = $port;

        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
