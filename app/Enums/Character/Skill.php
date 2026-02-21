<?php

namespace App\Enums\Character;

enum Skill: string
{
    case Accounting             = 'Accounting';
    case Anthropology           = 'Anthropology';
    case Appraise               = 'Appraise';
    case Archaeology            = 'Archaeology';
    case ArtCraft               = 'Art/Craft';
    case Charm                  = 'Charm';
    case Climb                  = 'Climb';
    case CreditRating           = 'Credit Rating';
    case CthulhuMythos          = 'Cthulhu Mythos';
    case Disguise               = 'Disguise';
    case Dodge                  = 'Dodge';
    case DriveAuto              = 'Drive Auto';
    case ElectricalRepair       = 'Electrical Repair';
    case FastTalk               = 'Fast Talk';
    case FightingBrawl          = 'Fighting (Brawl)';
    case FirearmsHandgun        = 'Firearms (Handgun)';
    case FirearmsRifleShotgun   = 'Firearms (Rifle/Shotgun)';
    case FirearmsSubmachineGun  = 'Firearms (Submachine Gun)';
    case FirstAid               = 'First Aid';
    case History                = 'History';
    case Intimidate             = 'Intimidate';
    case Jump                   = 'Jump';
    case LanguageOwn            = 'Language (Own)';
    case LanguageOther          = 'Language (Other)';
    case Law                    = 'Law';
    case LibraryUse             = 'Library Use';
    case Listen                 = 'Listen';
    case Locksmith              = 'Locksmith';
    case MechanicalRepair       = 'Mechanical Repair';
    case Medicine               = 'Medicine';
    case NaturalWorld           = 'Natural World';
    case Navigate               = 'Navigate';
    case Occult                 = 'Occult';
    case OperateHeavyMachinery  = 'Operate Heavy Machinery';
    case Persuade               = 'Persuade';
    case PilotBoat              = 'Pilot (Boat)';
    case PilotAircraft          = 'Pilot (Aircraft)';
    case Psychoanalysis         = 'Psychoanalysis';
    case Psychology             = 'Psychology';
    case Ride                   = 'Ride';
    case ScienceAstronomy       = 'Science (Astronomy)';
    case ScienceBiology         = 'Science (Biology)';
    case ScienceChemistry       = 'Science (Chemistry)';
    case ScienceGeology         = 'Science (Geology)';
    case SciencePhysics         = 'Science (Physics)';
    case SleightOfHand          = 'Sleight of Hand';
    case SpotHidden             = 'Spot Hidden';
    case Stealth                = 'Stealth';
    case Survival               = 'Survival';
    case Swim                   = 'Swim';
    case Throw                  = 'Throw';
    case Track                  = 'Track';

    /**
     * Base value as printed on the CoC 7e character sheet.
     * Dodge and Language (Own) are derived from stats and handled separately.
     */
    public function baseValue(): int
    {
        return match($this) {
            self::Accounting            => 5,
            self::Anthropology          => 1,
            self::Appraise              => 5,
            self::Archaeology           => 1,
            self::ArtCraft              => 5,
            self::Charm                 => 15,
            self::Climb                 => 20,
            self::CreditRating          => 0,
            self::CthulhuMythos         => 0,
            self::Disguise              => 5,
            self::Dodge                 => 0, // DEX / 2 — set in seeder
            self::DriveAuto             => 20,
            self::ElectricalRepair      => 10,
            self::FastTalk              => 5,
            self::FightingBrawl         => 25,
            self::FirearmsHandgun       => 20,
            self::FirearmsRifleShotgun  => 25,
            self::FirearmsSubmachineGun => 15,
            self::FirstAid              => 30,
            self::History               => 5,
            self::Intimidate            => 15,
            self::Jump                  => 20,
            self::LanguageOwn           => 0, // EDU — set in seeder
            self::LanguageOther         => 1,
            self::Law                   => 5,
            self::LibraryUse            => 20,
            self::Listen                => 20,
            self::Locksmith             => 1,
            self::MechanicalRepair      => 10,
            self::Medicine              => 1,
            self::NaturalWorld          => 10,
            self::Navigate              => 10,
            self::Occult                => 5,
            self::OperateHeavyMachinery => 1,
            self::Persuade              => 10,
            self::PilotBoat             => 1,
            self::PilotAircraft         => 1,
            self::Psychoanalysis        => 1,
            self::Psychology            => 10,
            self::Ride                  => 5,
            self::ScienceAstronomy      => 1,
            self::ScienceBiology        => 1,
            self::ScienceChemistry      => 1,
            self::ScienceGeology        => 1,
            self::SciencePhysics        => 1,
            self::SleightOfHand         => 10,
            self::SpotHidden            => 25,
            self::Stealth               => 20,
            self::Survival              => 10,
            self::Swim                  => 20,
            self::Throw                 => 20,
            self::Track                 => 10,
        };
    }
}
