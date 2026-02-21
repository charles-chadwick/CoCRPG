<?php

namespace App\Enums;

enum PlayerStat: string
{
    case Strength = 'Strength';
    case Dexterity = 'Dexterity';
    case Constitution = 'Constitution';
    case Intelligence = 'Intelligence';
    case Wisdom = 'Wisdom';
    case Charisma = 'Charisma';
}
