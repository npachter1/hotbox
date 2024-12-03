<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\Command;
use App\Helpers\EscposTools\Parser\Command\TextContainer;

class TextCmd extends Command implements TextContainer
{
    private $str = "";

    public function addChar($char)
    {
        if (isset(Printout::$tree[$char])) {
            // Reject ESC/POS control chars.
            return false;
        }
        $this -> str .= iconv('CP437', 'UTF-8', $char);
        return true;
    }

    public function getText()
    {
        return $this -> str;
    }
}
