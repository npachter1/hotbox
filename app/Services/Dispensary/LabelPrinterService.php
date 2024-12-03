<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;
use App\Models\AppSchema;
use Illuminate\Support\Arr;
use App\Helpers\ArrayToXML;
use App\Helpers\Zpl\Constant\ZebraFont;
use App\Helpers\Zpl\Constant\ZebraPrintMode;
use App\Helpers\Zpl\Model\Element\ZebraGraficBox;
use App\Helpers\Zpl\Model\Element\ZebraText;
use App\Helpers\Zpl\Model\Element\ZebraImage;
use App\Helpers\Zpl\Model\ZebraLabel;
use App\Helpers\Zpl\Model\Element\ZebraBarCode128;
use App\Helpers\Zpl\Model\PrinterOptions;

class LabelPrinterService extends BaseService
{

    public function generateLabel(array $data)
    {
        $printerType = array_get($data, 'type', null);

        if($printerType === "DYMO")
        {
            $labelId = array_get($data, 'labelId', 'LargeShipping');
            $uiSize = array_get($data, 'UISize', null);
            return base64_encode($this->getDymoLabel($labelId, $uiSize)); //because this is an xml file needs to be base64 encoded
        }
        if($printerType === "Zebra")
        {
            $width = array_get($data, 'labelWidthInches', null);
            $length = array_get($data, 'labelLengthInches', null);
            $barcode = array_get($data, 'barcode', null);
            $labelText = array_get($data, 'labelText', null);
            $copies = array_get($data, 'copies', 1);
            return $this->getZebraLabel($width,$length, $barcode, $labelText, $copies);
        }
    }

    public function getDymoLabel($labelId, $uiSize)
    {
        $schema = json_decode(json_encode(AppSchema::getSchema('dymolabel_list')),true);
            
        $filtered = Arr::first($schema, function ($value, $key) use ($labelId){
            return ($value['Id'] === $labelId);
        });

        $x = 335.9998;
        $y = 335.9998;
        $rX = 270;
        $rY = 270;
        $barcodeHeight = 622.1956;
        $textY = $y + $barcodeHeight + 80;

        //TODO: Add support for portrait labels
        $heightInTwips  = $this->fractionToDec($uiSize['inch']['@attributes']['Height']) * 1440;
        $widthInTwips= $this->fractionToDec($uiSize['inch']['@attributes']['Width']) * 1440;

        $printAreaLength = ($heightInTwips - ($rX/M_PI)) - $x;
        $printAreaRemainingHeight = ($widthInTwips - ($rY/M_PI)) - $textY;

        $schema = Arr::only((array)$filtered, array('@root','@attributes','PaperOrientation','Id','PaperName','DrawCommands'));

        Arr::set($schema,'ObjectInfo.ImageObject.Name', 'GRAPHIC');
        Arr::set($schema,'ObjectInfo.ImageObject.ForeColor.@attributes',array('Alpha'=>'255','Red'=>'0','Green'=>'0','Blue'=>'0'));
        Arr::set($schema,'ObjectInfo.ImageObject.BackColor.@attributes',array('Alpha'=>'0','Red'=>'255','Green'=>'255','Blue'=>'255'));
        Arr::set($schema,'ObjectInfo.ImageObject.LinkedObjectName','');
        Arr::set($schema,'ObjectInfo.ImageObject.Rotation','Rotation0');
        Arr::set($schema,'ObjectInfo.ImageObject.IsMirrored','False');
        Arr::set($schema,'ObjectInfo.ImageObject.IsVariable','False');
        Arr::set($schema,'ObjectInfo.ImageObject.Image','');
        Arr::set($schema,'ObjectInfo.ImageObject.ScaleMode','Uniform');
        Arr::set($schema,'ObjectInfo.ImageObject.BorderWidth','0');
        Arr::set($schema,'ObjectInfo.ImageObject.BorderColor.@attributes',array('Alpha'=>'255','Red'=>'0','Green'=>'0','Blue'=>'0'));
        Arr::set($schema,'ObjectInfo.ImageObject.HorizontalAlignment','Center');
        Arr::set($schema,'ObjectInfo.ImageObject.VerticalAlignment','Center');
        Arr::set($schema,'ObjectInfo.Bounds.@attributes',array('X'=>$x, 'Y'=>$textY, 'Width'=>$printAreaLength, 'Height'=>$printAreaRemainingHeight));

        Arr::set($schema, 'ObjectInfo2.BarcodeObject.Name', 'BARCODE');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.ForeColor.@attributes',array('Alpha'=>'255','Red'=>'0','Green'=>'0','Blue'=>'0'));
        Arr::set($schema,'ObjectInfo2.BarcodeObject.BackColor.@attributes',array('Alpha'=>'0','Red'=>'255','Green'=>'255','Blue'=>'255'));
        Arr::set($schema,'ObjectInfo2.BarcodeObject.LinkedObjectName','');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.Rotation','Rotation0');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.IsMirrored','False');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.IsVariable','False');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.Text','');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.Type','Code128Auto');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.Size','Small');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.TextPosition','Bottom');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.TextFont.@attributes',array('Family'=>'Helvetica', 'Size'=>'10', 'Bold'=>'False', 'Italic'=>'False','Underline'=>'False','Strikeout'=>'False'));
        Arr::set($schema,'ObjectInfo2.BarcodeObject.CheckSumFont.@attributes',array('Family'=>'Helvetica', 'Size'=>'10', 'Bold'=>'False', 'Italic'=>'False','Underline'=>'False','Strikeout'=>'False'));
        Arr::set($schema,'ObjectInfo2.BarcodeObject.TextEmbedding','None');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.ECLevel','0');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.HorizontalAlignment','Center');
        Arr::set($schema,'ObjectInfo2.BarcodeObject.QuietZonesPadding.@attributes',array('Left'=>'0', 'Right'=>'0', 'Top'=>'0', 'Bottom'=>'0'));
        Arr::set($schema,'ObjectInfo2.Bounds.@attributes',array('X'=>$x, 'Y'=>$y, 'Width'=>$printAreaLength, 'Height'=>$barcodeHeight));

        $xml = ArrayToXML::createXML('Dummy', (array)$schema);
        $xml = str_replace('ObjectInfo2','ObjectInfo',$xml->saveXML());
        return $xml;

    }

    public static function _getDymoLabelList(){

        $schema = json_decode(json_encode(AppSchema::getSchema('dymolabeltype_list')),true);

        $new_array = array();
        foreach ($schema['LabelType'] as $value) {
            
            $new_array[] = Arr::only((array)$value, array('CategoryId','Id','UISize','DefaultOrientation','Names','en'));

        }

        return $new_array;
    }

    public function getZebraLabel($labelWidthInches, $labelLengthInches, $barcode, $labelText = array(), $copies = 1)
    {
        $labelWidth = (int) ($labelWidthInches * 203);
        $labelLength = (int) ($labelLengthInches * 203);
        $barcodeX = (int) ($labelWidth * 0.21); //This seems to be the optimum ratio to adjust the centering of the barcode

        $zebraLabel = new ZebraLabel($labelWidth,$labelLength); //2.25x2 = 457x406 203dpi

        $zebraLabel->setDefaultZebraFont(new ZebraFont(ZebraFont::ZEBRA_ZERO));

        $zebraLabel->addElement(new ZebraBarCode128($barcodeX, 110, $barcode, 80, 1, true, false));

        $pointsY = 160;

        foreach ($labelText as $key => $value) {
            $zebraLabel->addElement(new ZebraText(1, $pointsY, $value, 5, $zebraLabel->getWidthDots()));
            $pointsY += 20 + 10;
        }

        $zpl = $zebraLabel->getZplCode();

        return str_repeat($zpl,$copies);
    }

    private function fractionToDec($input) {
        if (strpos($input, '/') === FALSE) {
            $result = $input;
        } else {
            $fraction = array('whole' => 0);
            preg_match('/^((?P<whole>\d+)(?=\s))?(\s*)?(?P<numerator>\d+)\/(?P<denominator>\d+)$/', $input, $fraction);
            $result = $fraction['whole'];
    
            if ($fraction['denominator'] > 0)
                $result += $fraction['numerator'] / $fraction['denominator'];
        }
    
        return $result;
    }


    public function renderTextAsImage()
    {
        $text='test@example.com';
        $string = $text;                                            
        $font   = 3;
        $width  = ImageFontWidth($font) * strlen($string);
        $height = ImageFontHeight($font);
        $im = @imagecreate ($width,$height);
        $background_color = imagecolorallocate ($im, 255, 255, 255); //white background
        $text_color = imagecolorallocate ($im, 0, 0,0);//black text
        imagestring ($im, $font, 0, 0, $string, $text_color);
        ob_start();
        imagepng($im);
        $imstr = base64_encode(ob_get_clean());
        imagedestroy($im);
        return $imstr;
    }

}