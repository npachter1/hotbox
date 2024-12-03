<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\CommandOneArg;
use App\Helpers\EscposTools\Parser\Command\InlineFormattingCmd;
use App\Helpers\EscposTools\Parser\Context\InlineFormatting;

class EnableEmphasisCmd extends CommandOneArg implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $formatting -> setBold($this -> getArg() == 1);
    }
}
