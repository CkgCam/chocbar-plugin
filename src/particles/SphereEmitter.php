<?php

declare(strict_types=1);

namespace ckgcam\chocbar\particles;

use pocketmine\math\Vector3;
use pocketmine\world\particle\Particle;
use pocketmine\world\particle\DustParticle;
use pocketmine\world\World;
use pocketmine\math\AxisAlignedBB;

class SphereEmitter {

    private float $radius;
    private int $points;
    private Particle $particle;

    public function __construct(float $radius = 1.5, int $points = 100, ?Particle $particle = null) {
        $this->radius = $radius;
        $this->points = $points;
        $this->particle = $particle ?? new DustParticle(255, 0, 0); // default red dust
    }

    public function emit(Vector3 $center, World $world): void {
        for ($i = 0; $i < $this->points; ++$i) {
            $theta = deg2rad(mt_rand(0, 360));
            $phi = deg2rad(mt_rand(0, 180));

            $x = $center->x + $this->radius * sin($phi) * cos($theta);
            $y = $center->y + $this->radius * cos($phi);
            $z = $center->z + $this->radius * sin($phi) * sin($theta);

            $world->addParticle(new Vector3($x, $y, $z), $this->particle);
        }
    }
}