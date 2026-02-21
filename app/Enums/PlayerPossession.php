<?php

namespace App\Enums;

enum PlayerPossession: string
{
    case Weapon        = 'Weapon';
    case Essential     = 'Essential';
    case Arcane        = 'Arcane';
    case Investigative = 'Investigative';
    case Key           = 'Key';
}
