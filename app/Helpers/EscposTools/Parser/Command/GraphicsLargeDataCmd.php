<?php
namespace App\Helpers\EscposTools\Parser\Command;

use App\Helpers\EscposTools\Parser\Command\LargeDataCmd;
use App\Helpers\EscposTools\Parser\Command\GraphicsDataCmd;

class GraphicsLargeDataCmd extends LargeDataCmd
{
    public function getSubCommand($m, $fn, $len)
    {
        // Same as regular graphics commands, just with more data!
        return GraphicsDataCmd::subCommandLookup($m, $fn, $len);
    }
}
