<?php

namespace App\Helpers\Zpl\Model\Element;

use App\Helpers\Zpl\Model\ZebraElement;
use App\Helpers\Zpl\Utils\ZplUtils;
use Zebra\Zpl\Image;
use Zebra\Zpl\GdDecoder;

/**
 * Zebra element to create image
 * 
 * Zpl command : 
 * 
 * @author matthiasvets
 * 
 */
class ZebraImage extends ZebraElement {

    private $ressource;
    private $compression;
    /**
     *
     * @param type $positionX
     * @param type $positionY
     * @param string $path
     * @param string $compression ['A' => Ascii data | 'B'=> Binary data]
     */
    public function __construct($positionX, $positionY, $path, $compression = 'A') {
        $decoder = GdDecoder::fromPath($path);
        $this->ressource   = new Image($decoder);
        $this->compression = $compression;
        $this->positionX   = $positionX;
        $this->positionY      = $positionY;
    }

    /**
     *
     *  {@inheritdoc}
     */
    public function getZplCode($printerOptions = null) {
        $bytesPerRow = $this->ressource->width();
        $byteCount   = $fieldCount  = $bytesPerRow * $this->ressource->height();
        $zpl = '';
        $zpl .= $this->getZplCodePosition();
        $zpl .= "\n";
        $zpl .= ZplUtils::zplCommand("GF", [
                    $this->compression,
                    $byteCount,
                    $fieldCount,
                    $bytesPerRow,
                    $this->ressource->toAscii()]);
        $zpl .= "^FS";
        $zpl .= "\n";
        return $zpl;
    }

}
