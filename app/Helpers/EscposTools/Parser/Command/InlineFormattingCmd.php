<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Context\InlineFormatting;

interface InlineFormattingCmd
{
    public function applyToInlineFormatting(InlineFormatting $formatting);
}
