<?php

namespace App\Helpers;


class Conversions
{
    
    public static function uomAbbreviationToName($abbr) {
        $name = null;
        switch ($abbr) {
            case 'g':
                $name = 'Grams';
                break;
            case 'oz':
                $name = 'Ounces';
                break;
            case 'mg':
                $name = 'Milligrams';
                break;
            case 'kg':
                $name = 'Kilograms';
                break;
            case 'lb':
                $name = 'Pounds';
                break;
            case 'ea':
                $name = 'Each';
        }
        return $name;
    }

    public function uomNameToAbbreviation($name) {
        $abbr = null;
        switch ($name) {
            case 'Grams':
                $abbr = 'g';
                break;
            case 'Ounces':
                $abbr = 'oz';
                break;
            case 'Milligrams':
                $abbr = 'mg';
                break;
            case 'Kilograms':
                $abbr = 'kg';
                break;
            case 'Pounds':
                $abbr = 'lb';
                break;
            case 'Each':
                $abbr = 'ea';
                break;
        }
        return $abbr;
    }

    public function convertWeight($weight, $uom_abbr, $new_uom_abbr, $user) {
        if ($uom_abbr == $new_uom_abbr)
            return $weight;

        $grams_per_ounce = $user->location->settings->grams_per_ounce;
        $convert = [
            'g' => 1,
            'oz' => ($grams_per_ounce ? $grams_per_ounce : 28.35),
            'mg' => .001,
            'kg' => 1000,
            'lb' => 453.59
        ];
        
        // convert $weight to grams
        $weight = $weight * $convert[$uom_abbr];
        // convert to new unit of measure
        $weight = $weight / $convert[$new_uom_abbr];

        return $weight;
    }

}
