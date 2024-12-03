<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\Command;
use App\Helpers\EscposTools\Parser\Context\ParserContext;

abstract class EscposCommand extends Command
{
    protected $stack;

    public function __construct(ParserContext $context, array $stack)
    {
        parent::__construct($context);
        $this -> stack = $stack;
    }
}
