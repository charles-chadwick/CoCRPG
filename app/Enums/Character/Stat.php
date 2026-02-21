<?php

namespace App\Enums\Character;

enum Stat: string
{
    case Strength = 'Strength';
    case Constitution = 'Constitution';
    case Size = 'Size';
    case Dexterity = 'Dexterity';
    case Appearance = 'Appearance';
    case Intelligence = 'Intelligence';
    case Power = 'Power';
    case Education = 'Education';
    case Luck = 'Luck';
}
