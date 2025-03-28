<?php
declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use pocketmine\entity\Human;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\math\Vector3;

class HumanNPC extends Human {

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(1.8, 0.6);
    }

    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setImmobile(true);
        $this->setCanSaveWithChunk(false);
    }

    public function attack(EntityDamageEvent $source): void {
        $source->cancel(); // No damage
    }

    public function onUpdate(int $currentTick): bool {
        $this->setMotion(Vector3::zero()); // Freeze in place
        return parent::onUpdate($currentTick);
    }

    public function getDrops(): array {
        return []; // No item drops
    }

    public function canBePushed(): bool {
        return false;
    }

    public function canBeKnockedBack(): bool {
        return false;
    }
}
