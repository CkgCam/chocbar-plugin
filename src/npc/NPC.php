<?php

declare(strict_types=1);

namespace ckgcam\chocbar\npc;

use pocketmine\entity\Human;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

use pocketmine\world\particle\FlameParticle;

class NPC extends Human {
    private string $npcId;
    private bool $showParticles = false;

    public function setNpcId(string $id): void {
        $this->npcId = $id;
    }

    public function enableParticles(bool $value = true): void {
        $this->showParticles = $value;
    }

    public function getNpcId(): string {
        return $this->npcId;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(1.8, 0.6);
    }

    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setCanSaveWithChunk(false);
        $this->setNameTagVisible(true);
        $this->setNameTagAlwaysVisible(true);
    }

    public function onUpdate(int $currentTick): bool {
        if ($this->showParticles && $currentTick % 10 === 0) {
            $this->location->getWorld()->addParticle($this->location->add(0, 1, 0), new FlameParticle());
        }

        $this->setMotion(Vector3::zero());
        $this->teleport($this->location);
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
