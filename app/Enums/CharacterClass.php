<?php

namespace App\Enums;

enum CharacterClass: string
{
    case Warrior = 'Warrior';
    case Mage = 'Mage';
    case Rogue = 'Rogue';
    case Cleric = 'Cleric';
    case Ranger = 'Ranger';
    case Paladin = 'Paladin';
    case Bard = 'Bard';
    case Druid = 'Druid';
}
