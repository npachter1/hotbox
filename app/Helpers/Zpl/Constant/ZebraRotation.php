<?php


namespace App\Helpers\Zpl\Constant;

/**
 *
 */
class ZebraRotation {

    const NORMAL           = "N";
    const ROTATE_90        = "R";
    const INVERTED         = "I";
    const READ_FROM_BOTTOM = "B";

    private $letter;
    /**
     *
     * @param string $letter
     */
    public function __construct($letter) {
        $this->letter = $letter;
    }

    /**
     * @return string
     */
    public function getLetter() {
        return $this->letter;
    }

}
