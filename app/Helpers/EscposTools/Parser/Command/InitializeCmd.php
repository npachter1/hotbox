<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\EscposCommand;
use App\Helpers\EscposTools\Parser\Command\InlineFormattingCmd;
use App\Helpers\EscposTools\Parser\Context\InlineFormatting;

class InitializeCmd extends EscposCommand implements InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting)
    {
        $formatting -> reset();
    }
}
