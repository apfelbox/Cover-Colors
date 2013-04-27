<?php

namespace Image\Histogram;

use Color\Color;


class ColorBucket implements \Countable
{
    /**
     * @var Color[]
     */
    private $colors = array();


    /**
     * @var Color|null
     */
    private $representativeColor = null;



    /**
     * @param Color $color
     */
    public function __construct (Color $color)
    {
        $this->addColor($color);
    }



    /**
     * @param Color $color
     */
    public function addColor (Color $color)
    {
        $this->colors[] = $color;

        // need to recompute the representative color
        $this->representativeColor = null;
    }



    /**
     * Returns a representative color of this bucket
     *
     * @return Color
     */
    public function getRepresentativeColor ()
    {
        if (is_null($this->representativeColor))
        {
            $r = 0;
            $g = 0;
            $b = 0;
            $count = count($this);

            foreach ($this->colors as $color)
            {
                $r += $color->getRed();
                $g += $color->getGreen();
                $b += $color->getBlue();
            }

            $this->representativeColor = new Color($r / $count, $g / $count, $b / $count);
        }

        return $this->representativeColor;
    }



    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count ()
    {
        return count($this->colors);
    }


}