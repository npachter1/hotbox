<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\CommandOneArg;
use App\Helpers\EscposTools\Parser\Command\InlineFormattingCmd;
use App\Helpers\EscposTools\Parser\Context\InlineFormatting;

class EnableUnderlineCmd extends CommandOneArg implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $arg = $this -> getArg();
        if ($arg === 0 || $arg === 48) {
            $formatting -> setUnderline(0);
        } else if ($arg === 1 || $arg === 49) {
            $formatting -> setUnderline(1);
        } else if ($arg === 2 || $arg === 50) {
            $formatting -> setUnderline(2);
        }
    }
}
