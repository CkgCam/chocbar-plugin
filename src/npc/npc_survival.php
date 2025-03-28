<?php

declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class npc_survival extends Living {

    public static function getNetworkTypeId(): string {
        return "chocbar:survival";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(1.8, 0.6);
    }

    public function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setNameTag("§aCustom NPC");
        $this->setNameTagAlwaysVisible(true);
        $this->setScale(1);
        $this->setSilent(true);
    }

    public function getName(): string {
        return "Custom NPC";
    }

    public function attack(EntityDamageEvent $source): void {
        $source->cancel(); // Immune to damage
    }

    public function onUpdate(int $currentTick): bool {
        $this->setMotion(Vector3::zero()); // Stop motion
        $this->teleport($this->location);  // Reposition to prevent sliding
        return parent::onUpdate($currentTick);
    }

    public function canBePushed(): bool {
        return false;
    }

    public function canBeKnockedBack(): bool {
        return false;
    }

    public function isPushable(): bool {
        return false;
    }

    public function hasGravity(): bool {
        return false;
    }

    public function canSaveWithChunk(): bool {
        return false;
    }

    public function getDrops(): array {
        return [];
    }
}
