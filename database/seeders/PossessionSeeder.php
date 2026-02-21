<?php

namespace Database\Seeders;

use App\Enums\Character\Possession as PossessionType;
use App\Enums\ModifierSign;
use App\Models\Character;
use App\Models\Possession;
use Illuminate\Database\Seeder;

class PossessionSeeder extends Seeder
{
    /** @var array<string, array<string, int>> */
    private array $items = [
        'Weapon' => [
            '.38 Revolver'    => 30,
            '.45 Automatic'   => 40,
            '.22 Rifle'       => 25,
            'Shotgun'         => 35,
            'Hunting Knife'   => 5,
            'Switchblade'     => 3,
            'Baseball Bat'    => 2,
            'Fire Axe'        => 8,
            'Crowbar'         => 4,
            'Elephant Gun'    => 150,
            'Tommy Gun'       => 200,
            'Derringer'       => 15,
            'Machete'         => 6,
        ],
        'Essential' => [
            'Flashlight'      => 3,
            'First Aid Kit'   => 10,
            'Rope (50 ft)'    => 5,
            'Lantern'         => 4,
            'Matches'         => 1,
            'Canteen'         => 2,
            'Compass'         => 5,
            'Binoculars'      => 20,
            'Camera'          => 25,
            'Handcuffs'       => 8,
            'Medical Bag'     => 30,
            'Camping Gear'    => 15,
        ],
        'Arcane' => [
            'Necronomicon'              => 500,
            'De Vermis Mysteriis'       => 400,
            'Unausprechlichen Kulten'   => 300,
            'The King in Yellow'        => 200,
            'Elder Sign Amulet'         => 100,
            'Enchanted Dagger'          => 150,
            'Crystal Ball'              => 50,
            'Ritual Candles'            => 10,
            'Cultist Robes'             => 20,
            'Protective Talisman'       => 75,
            'The Pnakotic Manuscripts'  => 350,
            'Cultes des Goules'         => 450,
        ],
        'Investigative' => [
            'Magnifying Glass'  => 5,
            'Notebook'          => 1,
            'Fingerprint Kit'   => 15,
            'Press Pass'        => 0,
            'Detective\'s Badge' => 0,
            'Lockpick Set'      => 10,
            'Disguise Kit'      => 25,
            'Stethoscope'       => 20,
            'Tape Measure'      => 3,
            'Evidence Bags'     => 5,
        ],
        'Key' => [
            'Skeleton Key'            => 5,
            'Safe Deposit Box Key'    => 0,
            'Bank Book'               => 0,
            'Private Journal'         => 2,
            'Letter of Introduction'  => 0,
            'Map to Unknown Location' => 10,
            'Ship Manifest'           => 5,
            'Ancient Map'             => 50,
        ],
    ];

    public function run(): void
    {
        $pool = [];
        foreach ($this->items as $type => $names) {
            foreach ($names as $name => $value) {
                $pool[] = ['type' => $type, 'name' => $name, 'value' => $value];
            }
        }

        shuffle($pool);
        $selected = array_slice($pool, 0, 50);

        $possessions = collect($selected)->map(fn (array $item) => Possession::firstOrCreate(
            ['type' => PossessionType::from($item['type']), 'name' => $item['name']],
            ['value' => $item['value']],
        ));

        Character::query()->each(function (Character $character) use ($possessions): void {
            $assigned = $possessions->random(rand(3, 8));

            foreach ($assigned as $possession) {
                $pivot = [];

                $modifier = fake()->optional()->numberBetween(-10, 10);
                if ($modifier) {
                    $pivot['modifier'] = $modifier;
                    $pivot['modifier_sign'] = fake()->optional()->randomElement(ModifierSign::cases())?->value;
                }

                $character->possessions()->attach($possession->id, $pivot);
            }
        });
    }
}
