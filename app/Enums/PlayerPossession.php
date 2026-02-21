<?php

namespace App\Enums;

enum PlayerPossession: string
{
    case Weapon = 'Weapon';
    case Armor = 'Armor';
    case Shield = 'Shield';
    case Potion = 'Potion';
    case Scroll = 'Scroll';
}
