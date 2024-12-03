<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\CommandOneArg;
use App\Helpers\EscposTools\Parser\Command\InlineFormattingCmd;
use App\Helpers\EscposTools\Parser\Context\InlineFormatting;

class SelectPrintModeCmd extends CommandOneArg implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $arg = $this -> getArg();
        $formatting -> setFont($arg & 1);
        $formatting -> setBold($arg & 8);
        $formatting -> setHeightMultiple($arg & 16 ? 2 : 1);
        $formatting -> setWidthMultiple($arg & 32 ? 2 : 1);
        $formatting -> setUnderline($arg & 128);
    }
}
