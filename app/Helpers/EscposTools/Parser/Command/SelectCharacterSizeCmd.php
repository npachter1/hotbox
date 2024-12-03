<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\CommandOneArg;
use App\Helpers\EscposTools\Parser\Command\InlineFormattingCmd;
use App\Helpers\EscposTools\Parser\Context\InlineFormatting;

class SelectCharacterSizeCmd extends CommandOneArg implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $arg = $this -> getArg();
        // TODO Add height multiples from this command
        $formatting -> setWidthMultiple(2);
        $width = intdiv($arg, 16) + 1;
        $height = ($arg % 16) + 1;
        $formatting -> setWidthMultiple($width);
        $formatting -> setHeightMultiple($height);
    }
}
