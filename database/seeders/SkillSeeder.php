<?php

namespace Database\Seeders;

use App\Enums\Character\Occupation;
use App\Enums\Character\Skill;
use App\Enums\Character\Stat as CharacterStat;
use App\Models\Character;
use App\Models\Skill as SkillModel;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Occupation skill pools — 8 skills per occupation (CoC 7e rules).
     *
     * @var array<string, list<string>>
     */
    private array $occupationSkills = [
        'Antiquarian'          => ['Appraise', 'Art/Craft', 'History', 'Library Use', 'Occult', 'Spot Hidden', 'Language (Other)', 'Persuade'],
        'Artist'               => ['Art/Craft', 'History', 'Language (Other)', 'Library Use', 'Natural World', 'Spot Hidden', 'Psychology', 'Charm'],
        'Author'               => ['History', 'Language (Other)', 'Library Use', 'Natural World', 'Occult', 'Psychology', 'Spot Hidden', 'Charm'],
        'Clergy'               => ['Accounting', 'Language (Other)', 'History', 'Library Use', 'Listen', 'Persuade', 'Psychology', 'Spot Hidden'],
        'Criminal'             => ['Disguise', 'Fast Talk', 'Fighting (Brawl)', 'Intimidate', 'Locksmith', 'Sleight of Hand', 'Stealth', 'Psychology'],
        'Dilettante'           => ['Art/Craft', 'Firearms (Handgun)', 'Language (Other)', 'Library Use', 'Ride', 'Credit Rating', 'Charm', 'Spot Hidden'],
        'Doctor of Medicine'   => ['First Aid', 'Library Use', 'Medicine', 'Persuade', 'Psychoanalysis', 'Psychology', 'Science (Biology)', 'Spot Hidden'],
        'Engineer'             => ['Electrical Repair', 'Library Use', 'Mechanical Repair', 'Operate Heavy Machinery', 'Science (Physics)', 'Spot Hidden', 'Appraise', 'Navigate'],
        'Entertainer'          => ['Art/Craft', 'Disguise', 'Charm', 'Fast Talk', 'Listen', 'Psychology', 'Persuade', 'Spot Hidden'],
        'Explorer'             => ['Climb', 'Firearms (Rifle/Shotgun)', 'History', 'Jump', 'Natural World', 'Navigate', 'Swim', 'Track'],
        'Farmer'               => ['Drive Auto', 'Electrical Repair', 'First Aid', 'Mechanical Repair', 'Natural World', 'Operate Heavy Machinery', 'Track', 'Survival'],
        'Hunter'               => ['Firearms (Rifle/Shotgun)', 'Listen', 'Natural World', 'Navigate', 'Spot Hidden', 'Stealth', 'Survival', 'Track'],
        'Journalist'           => ['Fast Talk', 'History', 'Language (Other)', 'Library Use', 'Psychology', 'Spot Hidden', 'Persuade', 'Charm'],
        'Lawyer'               => ['Accounting', 'Fast Talk', 'Intimidate', 'Law', 'Library Use', 'Persuade', 'Psychology', 'History'],
        'Mechanic'             => ['Electrical Repair', 'Fighting (Brawl)', 'First Aid', 'Mechanical Repair', 'Operate Heavy Machinery', 'Locksmith', 'Drive Auto', 'Spot Hidden'],
        'Military Officer'     => ['Accounting', 'Firearms (Handgun)', 'History', 'Navigate', 'Persuade', 'Psychology', 'Fighting (Brawl)', 'Intimidate'],
        'Musician'             => ['Art/Craft', 'Charm', 'Fast Talk', 'Listen', 'Psychology', 'Persuade', 'Spot Hidden', 'Language (Other)'],
        'Nurse'                => ['First Aid', 'Library Use', 'Medicine', 'Psychology', 'Spot Hidden', 'Stealth', 'Listen', 'Persuade'],
        'Occultist'            => ['Anthropology', 'History', 'Library Use', 'Occult', 'Science (Astronomy)', 'Language (Other)', 'Charm', 'Persuade'],
        'Police Detective'     => ['Fighting (Brawl)', 'Firearms (Handgun)', 'Fast Talk', 'Intimidate', 'Law', 'Psychology', 'Spot Hidden', 'Stealth'],
        'Private Investigator' => ['Art/Craft', 'Disguise', 'Fast Talk', 'Law', 'Library Use', 'Psychology', 'Spot Hidden', 'Stealth'],
        'Professor'            => ['Library Use', 'Language (Other)', 'Psychology', 'Spot Hidden', 'History', 'Occult', 'Persuade', 'Anthropology'],
        'Sailor'               => ['Climb', 'Electrical Repair', 'First Aid', 'Mechanical Repair', 'Navigate', 'Pilot (Boat)', 'Spot Hidden', 'Swim'],
        'Scientist'            => ['Electrical Repair', 'Library Use', 'Mechanical Repair', 'Science (Biology)', 'Science (Chemistry)', 'Science (Physics)', 'Language (Other)', 'Spot Hidden'],
        'Soldier'              => ['Climb', 'Fighting (Brawl)', 'Firearms (Rifle/Shotgun)', 'First Aid', 'Stealth', 'Survival', 'Navigate', 'Intimidate'],
        'Spy'                  => ['Charm', 'Disguise', 'Fast Talk', 'Firearms (Handgun)', 'Language (Other)', 'Persuade', 'Sleight of Hand', 'Stealth'],
        'Undertaker'           => ['Accounting', 'Charm', 'Drive Auto', 'First Aid', 'Occult', 'Persuade', 'Psychology', 'Stealth'],
    ];

    public function run(): void
    {
        Character::query()->with('stats')->each(function (Character $character): void {
            $edu = $character->stats->firstWhere('name', CharacterStat::Education)?->value ?? 50;
            $int = $character->stats->firstWhere('name', CharacterStat::Intelligence)?->value ?? 50;
            $dex = $character->stats->firstWhere('name', CharacterStat::Dexterity)?->value ?? 50;

            // Build base values for every skill
            $skillValues = [];
            foreach (Skill::cases() as $skill) {
                $skillValues[$skill->value] = match($skill) {
                    Skill::Dodge       => intdiv($dex, 2),
                    Skill::LanguageOwn => $edu,
                    default            => $skill->baseValue(),
                };
            }

            // Occupation skill points: EDU × 4, distributed across 8 occupation skills
            $occupationPool = $this->occupationSkills[$character->occupation->value] ?? [];
            $skillValues    = $this->distributePoints($skillValues, $occupationPool, $edu * 4);

            // Personal interest points: INT × 2, distributed across any 4 skills freely
            $interestPool = collect(Skill::cases())
                ->reject(fn(Skill $s) => $s === Skill::CthulhuMythos)
                ->map(fn(Skill $s) => $s->value)
                ->shuffle()
                ->take(4)
                ->all();
            $skillValues = $this->distributePoints($skillValues, $interestPool, $int * 2);

            // Persist all skills
            foreach ($skillValues as $name => $value) {
                SkillModel::create([
                    'character_id' => $character->id,
                    'name'         => Skill::from($name),
                    'value'        => min(99, $value),
                ]);
            }
        });
    }

    /**
     * Distribute points across a skill pool, 1–20 points at a time per skill.
     *
     * @param  array<string, int> $skillValues
     * @param  list<string>       $pool
     * @return array<string, int>
     */
    private function distributePoints(array $skillValues, array $pool, int $points): array
    {
        if (empty($pool) || $points <= 0) {
            return $skillValues;
        }

        while ($points > 0) {
            $skill   = $pool[array_rand($pool)];
            $current = $skillValues[$skill] ?? 0;

            if ($current >= 99) {
                continue;
            }

            $add              = rand(1, min($points, 20, 99 - $current));
            $skillValues[$skill] = $current + $add;
            $points          -= $add;
        }

        return $skillValues;
    }
}
