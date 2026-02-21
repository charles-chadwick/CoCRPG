<?php

namespace App\Enums\Character;

enum Possession: string
{
    case Weapon        = 'Weapon';
    case Essential     = 'Essential';
    case Arcane        = 'Arcane';
    case Investigative = 'Investigative';
    case Key           = 'Key';
}
