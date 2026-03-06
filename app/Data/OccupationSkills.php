<?php

namespace App\Data;

class OccupationSkills
{
    /**
     * Occupation skill pools — 8 skills per occupation (CoC 7e rules).
     *
     * @var array<string, list<string>>
     */
    public const array SKILLS = [
        'Antiquarian' => ['Appraise', 'Art/Craft', 'History', 'Library Use', 'Occult', 'Spot Hidden', 'Language (Other)', 'Persuade'],
        'Artist' => ['Art/Craft', 'History', 'Language (Other)', 'Library Use', 'Natural World', 'Spot Hidden', 'Psychology', 'Charm'],
        'Author' => ['History', 'Language (Other)', 'Library Use', 'Natural World', 'Occult', 'Psychology', 'Spot Hidden', 'Charm'],
        'Clergy' => ['Accounting', 'Language (Other)', 'History', 'Library Use', 'Listen', 'Persuade', 'Psychology', 'Spot Hidden'],
        'Criminal' => ['Disguise', 'Fast Talk', 'Fighting (Brawl)', 'Intimidate', 'Locksmith', 'Sleight of Hand', 'Stealth', 'Psychology'],
        'Dilettante' => ['Art/Craft', 'Firearms (Handgun)', 'Language (Other)', 'Library Use', 'Ride', 'Credit Rating', 'Charm', 'Spot Hidden'],
        'Doctor of Medicine' => ['First Aid', 'Library Use', 'Medicine', 'Persuade', 'Psychoanalysis', 'Psychology', 'Science (Biology)', 'Spot Hidden'],
        'Engineer' => ['Electrical Repair', 'Library Use', 'Mechanical Repair', 'Operate Heavy Machinery', 'Science (Physics)', 'Spot Hidden', 'Appraise', 'Navigate'],
        'Entertainer' => ['Art/Craft', 'Disguise', 'Charm', 'Fast Talk', 'Listen', 'Psychology', 'Persuade', 'Spot Hidden'],
        'Explorer' => ['Climb', 'Firearms (Rifle/Shotgun)', 'History', 'Jump', 'Natural World', 'Navigate', 'Swim', 'Track'],
        'Farmer' => ['Drive Auto', 'Electrical Repair', 'First Aid', 'Mechanical Repair', 'Natural World', 'Operate Heavy Machinery', 'Track', 'Survival'],
        'Hunter' => ['Firearms (Rifle/Shotgun)', 'Listen', 'Natural World', 'Navigate', 'Spot Hidden', 'Stealth', 'Survival', 'Track'],
        'Journalist' => ['Fast Talk', 'History', 'Language (Other)', 'Library Use', 'Psychology', 'Spot Hidden', 'Persuade', 'Charm'],
        'Lawyer' => ['Accounting', 'Fast Talk', 'Intimidate', 'Law', 'Library Use', 'Persuade', 'Psychology', 'History'],
        'Mechanic' => ['Electrical Repair', 'Fighting (Brawl)', 'First Aid', 'Mechanical Repair', 'Operate Heavy Machinery', 'Locksmith', 'Drive Auto', 'Spot Hidden'],
        'Military Officer' => ['Accounting', 'Firearms (Handgun)', 'History', 'Navigate', 'Persuade', 'Psychology', 'Fighting (Brawl)', 'Intimidate'],
        'Musician' => ['Art/Craft', 'Charm', 'Fast Talk', 'Listen', 'Psychology', 'Persuade', 'Spot Hidden', 'Language (Other)'],
        'Nurse' => ['First Aid', 'Library Use', 'Medicine', 'Psychology', 'Spot Hidden', 'Stealth', 'Listen', 'Persuade'],
        'Occultist' => ['Anthropology', 'History', 'Library Use', 'Occult', 'Science (Astronomy)', 'Language (Other)', 'Charm', 'Persuade'],
        'Police Detective' => ['Fighting (Brawl)', 'Firearms (Handgun)', 'Fast Talk', 'Intimidate', 'Law', 'Psychology', 'Spot Hidden', 'Stealth'],
        'Private Investigator' => ['Art/Craft', 'Disguise', 'Fast Talk', 'Law', 'Library Use', 'Psychology', 'Spot Hidden', 'Stealth'],
        'Professor' => ['Library Use', 'Language (Other)', 'Psychology', 'Spot Hidden', 'History', 'Occult', 'Persuade', 'Anthropology'],
        'Sailor' => ['Climb', 'Electrical Repair', 'First Aid', 'Mechanical Repair', 'Navigate', 'Pilot (Boat)', 'Spot Hidden', 'Swim'],
        'Scientist' => ['Electrical Repair', 'Library Use', 'Mechanical Repair', 'Science (Biology)', 'Science (Chemistry)', 'Science (Physics)', 'Language (Other)', 'Spot Hidden'],
        'Soldier' => ['Climb', 'Fighting (Brawl)', 'Firearms (Rifle/Shotgun)', 'First Aid', 'Stealth', 'Survival', 'Navigate', 'Intimidate'],
        'Spy' => ['Charm', 'Disguise', 'Fast Talk', 'Firearms (Handgun)', 'Language (Other)', 'Persuade', 'Sleight of Hand', 'Stealth'],
        'Undertaker' => ['Accounting', 'Charm', 'Drive Auto', 'First Aid', 'Occult', 'Persuade', 'Psychology', 'Stealth'],
    ];
}
