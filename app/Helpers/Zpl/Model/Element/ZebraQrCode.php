<?php

namespace App\Helpers\Zpl\Model\Element;

use App\Helpers\Zpl\Model\ZebraElement;
use App\Helpers\Zpl\Utils\ZplUtils;

/**
 * Element to create a bar code 39
 * 
 * Zpl command : ^B3 and ^BY
 * 
 */
class ZebraQrCode extends ZebraElement {

    private $text;
    private $mfactor;

    /**
     *
     * @param float $positionX
     * @param float $positionY
     * @param string $text
     * @param int $dimension
     */
    public function __construct($positionX, $positionY, $text, $dimension = 10) {
        $this->positionX = $positionX;
        $this->positionY = $positionY;
        $this->mfactor   = $dimension;
        $this->model     = 2;
        $this->text      = $text;
    }
    /**
     *
     *  {@inheritdoc}
     */
    public function getZplCode($printerOptions = null)
    {
        $zpl = '';
        $zpl .= $this->getZplCodePosition();
        $zpl .= "\n";
        $zpl .= ZplUtils::zplCommandSautLigne("BQ", [
                    'N',
                    $this->model,
                    $this->mfactor,
        ]);
        $zpl .= "^FD";
        $zpl .= $this->text;
        $zpl .= ZplUtils::zplCommandSautLigne("FS");
        return $zpl;
    }

}
