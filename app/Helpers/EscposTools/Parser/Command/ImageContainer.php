<?php
namespace App\Helpers\EscposTools\Parser\Command;

interface ImageContainer
{
    public function getWidth();
    
    public function getHeight();
    
    public function asPng();
    
    public function asPbm();
}
