<?php

namespace Color;

class Color
{
    /**
     * The red component [0, 1.0]
     *
     * @var int
     */
    private $red;


    /**
     * The green component [0, 1.0]
     *
     * @var int
     */
    private $green;


    /**
     * The blue component [0, 1.0]
     *
     * @var int
     */
    private $blue;



    public function __construct ($red, $green, $blue)
    {
        $this->blue  = $blue;
        $this->green = $green;
        $this->red   = $red;
    }



    /**
     * Transforms a RGB value to a YUV value
     * Formula taken from http://en.wikipedia.org/wiki/YUV#Conversion_to.2Ffrom_RGB
     *
     * @return array [y, u, v]
     */
    private function getYUV ()
    {
        $y =  0.29900 * $this->red + 0.58700 * $this->green + 0.11400 * $this->blue;
        $u = -0.14713 * $this->red - 0.28886 * $this->green + 0.43600 * $this->blue;
        $v =  0.61500 * $this->red - 0.51499 * $this->green - 0.10001 * $this->blue;

        return array($y, $u, $v);
    }



    /**
     * Returns the color difference to another color
     * (= the euclidean distance in the color space)
     *
     * NOTE: actually, there is a square root missing here. But it is omitted, since these values are only compared.
     * The comparison result does not change, after calculating the square root of both values, but the calculation
     * itself is quite costly. So it is omitted.
     *
     * @param Color $otherColor
     *
     * @return float
     */
    public function getColorDifference (Color $otherColor)
    {
        $yuv1 = $this->getYUV();
        $yuv2 = $otherColor->getYUV();

        return sqrt(pow($yuv2[0] - $yuv1[0], 2) + pow($yuv2[1] - $yuv1[1], 2) + pow($yuv2[2] - $yuv1[2], 2));
    }



    /**
     * Returns the hex string
     *
     * @return string
     */
    public function getHexString ()
    {
        $red   = (int) ($this->red   * 255);
        $green = (int) ($this->green * 255);
        $blue  = (int) ($this->blue  * 255);

        return "#"
            . sprintf("%02s", base_convert($red,   10, 16))
            . sprintf("%02s", base_convert($green, 10, 16))
            . sprintf("%02s", base_convert($blue,  10, 16));
    }



    /**
     * Returns, whether this is a dark color
     *
     * @return bool
     */
    public function isDarkColor ()
    {
        return 0.5 > $this->getLuminance();
    }



    /**
     * Returns the luminance of the color
     *
     * @return float
     */
    public function getLuminance ()
    {
        return 0.299 * $this->red + 0.587 * $this->green + 0.114 * $this->blue;
    }



    /**
     * @return int
     */
    public function getBlue ()
    {
        return $this->blue;
    }



    /**
     * @return int
     */
    public function getGreen ()
    {
        return $this->green;
    }



    /**
     * @return int
     */
    public function getRed ()
    {
        return $this->red;
    }
}