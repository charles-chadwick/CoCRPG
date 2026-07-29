<?php

namespace App\Actions;

use App\Models\Character;

class UpdateCharacterAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Character $character, array $data): Character
    {
        $character->update($data);

        return $character;
    }
}
