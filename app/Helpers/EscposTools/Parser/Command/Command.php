<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Context\ParserContext;

abstract class Command
{
    protected $context;

    public function __construct(ParserContext $context)
    {
        $this -> context = $context;
    }

    public function addChar($char)
    {
        return false;
    }

    public function isAvailableAs($interface)
    {
        $className = get_called_class();
        if ($className == "App\Helpers\\EscposTools\\Parser\\Command\\$interface") {
            return true;
        }
        $impl = class_implements($this);
        return isset($impl["App\Helpers\\EscposTools\\Parser\\Command\\$interface"]);
    }
}
