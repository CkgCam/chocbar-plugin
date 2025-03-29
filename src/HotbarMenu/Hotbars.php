<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

class Hotbars {
    /** @var array[] */
    public static array $hub = [
        [
            "name" => "Navigator",
            "item" => "minecraft:compass",
            "enchanted" => false
        ],
        [
            "name" => "Info Book",
            "item" => "minecraft:book",
            "enchanted" => false
        ],
        [
            "name" => "Emerald Shop",
            "item" => "minecraft:emerald",
            "enchanted" => true
        ]
    ];

    /** @var array[] */
    public static array $survival = [
        [
            "name" => "Starter Axe",
            "item" => "minecraft:iron_axe",
            "enchanted" => true
        ],
        [
            "name" => "Starter Pickaxe",
            "item" => "minecraft:iron_pickaxe",
            "enchanted" => true
        ],
        [
            "name" => "Magic Stick",
            "item" => "minecraft:stick",
            "enchanted" => true
        ]
    ];
}
