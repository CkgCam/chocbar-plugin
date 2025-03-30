<?php

declare(strict_types=1);

namespace ckgcam\chocbar\HotbarMenu;

class Hotbars {
    /** @var array[] */
    public static array $hub = [
        [
            "name" => "Navigator",
            "item" => "compass",
            "enchanted" => false
        ],
        [
            "name" => "Info Book",
            "item" => "book",
            "enchanted" => false
        ],
        [
            "name" => "Emerald Shop",
            "item" => "emerald",
            "enchanted" => true
        ]
    ];

    /** @var array[] */
    public static array $survival = [
        [
            "name" => "Starter Axe",
            "item" => "iron_axe",
            "enchanted" => true
        ],
        [
            "name" => "Starter Pickaxe",
            "item" => "iron_pickaxe",
            "enchanted" => true
        ],
        [
            "name" => "Magic Stick",
            "item" => "stick",
            "enchanted" => true
        ]
    ];
}
