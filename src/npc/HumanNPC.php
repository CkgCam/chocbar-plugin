<?php
declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use pocketmine\entity\Human;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class HumanNPC extends Human {

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(1.8, 0.6);
    }

    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setCanSaveWithChunk(false); // Don't save NPCs to world
    }

    public function attack(EntityDamageEvent $source): void {
        $source->cancel(); // Can't take damage
    }

    public function onUpdate(int $currentTick): bool {
        // Cancel movement every tick
        $this->setMotion(Vector3::zero());
        $this->teleport($this->location); // Force back to exact position if it tries to slide
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
        return []; // Don't drop anything on death
    }
}
