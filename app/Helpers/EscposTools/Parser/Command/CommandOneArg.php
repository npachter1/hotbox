<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\EscposCommand;

class CommandOneArg extends EscposCommand
{
    private $arg = null;

    public function addChar($char)
    {
        if ($this -> arg === null) {
            $this -> arg = ord($char);
            return true;
        } else {
            return false;
        }
    }
    
    protected function getArg()
    {
        return $this -> arg;
    }
}
