<?php
declare(strict_types=1);

namespace ckgcam\chocbar;

use pocketmine\event\Listener;
use pocketmine\event\server\PacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

class ProtocolBypassListener implements Listener {

    public function onPacketReceive(PacketReceiveEvent $event): void {
        $packet = $event->getPacket();

        if ($packet instanceof LoginPacket) {
            $allowedProtocols = [
                ProtocolInfo::CURRENT_PROTOCOL,
                786
            ];

            if (in_array($packet->protocol, $allowedProtocols, true)) {
                $packet->protocol = ProtocolInfo::CURRENT_PROTOCOL;
            }
        }
    }
}
