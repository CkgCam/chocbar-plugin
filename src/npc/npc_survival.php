<?php

declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\EntityDataHelper;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class npc_survival extends Living {

    public static function getNetworkTypeId(): string {
        return "chocbar:survival"; // match entity.entity.json identifier
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(1.8, 0.6); // height, width
    }

    protected function getInitialDragMultiplier(): float {
        return 0.0;
    }

    protected function getInitialGravity(): float {
        return 0.0; // so it doesn't fall
    }

    public function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setNameTag("§aCustom NPC");
        $this->setNameTagAlwaysVisible(true);
        $this->setImmobile(true);
    }

    public function getName(): string {
        return "CustomNPC";
    }

    public function getDefaultNetworkId(): string {
        return static::getNetworkTypeId();
    }
}
