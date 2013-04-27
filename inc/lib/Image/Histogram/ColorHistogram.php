<?php

namespace Image\Histogram;

use Color\Color;

/**
 * This class holds the data of the color histogram.
 *
 * It converts all rgb values internally into yuv values, since the
 * color distance in the YUV space is a better match to the human
 * color reception than the distance in RGB space.
 *
 * Class ColorHistogram
 * @package Image
 */
class ColorHistogram implements \IteratorAggregate
{
    /**
     * The histogram data
     *
     * @var ColorBucket[]
     */
    private $histogram = array();


    /**
     * The threshold of difference between two colors, below which the two colors are
     * regarded as similar.
     *
     * @var float
     */
    private $colorSimilarityThreshold;



    /**
     * @param $colorSimilarityThreshold
     */
    public function __construct ($colorSimilarityThreshold)
    {
        $this->colorSimilarityThreshold = $colorSimilarityThreshold;
    }



    /**
     * Inserts a color into the histogram
     *
     * @param Color $color
     */
    public function insertColor (Color $color)
    {
        $this->insertColorIntoBucket($color);
    }



    /**
     * Inserts a color into the histogram
     *
     * @param Color $color
     */
    private function insertColorIntoBucket (Color $color)
    {
        if (!empty($this->histogram))
        {
            foreach ($this->histogram as $colorBucket)
            {
                if ($this->colorSimilarityThreshold >= $color->getColorDifference($colorBucket->getRepresentativeColor()))
                {
                    $colorBucket->addColor($color);
                    return;
                }
            }
        }

        $this->histogram[] = new ColorBucket($color);
    }



    /**
     * Sorts the histogram by descending order
     */
    public function sortAndFilter ($minimumEntries)
    {
        // first: filter
        $this->histogram = array_filter($this->histogram,
            function (ColorBucket $colorBucket) use ($minimumEntries)
            {
                return count($colorBucket) >= $minimumEntries;
            }
        );


        // then: sort
        usort($this->histogram,
            function (ColorBucket $colorBucket1, ColorBucket $colorBucket2)
            {
                return count($colorBucket2) - count($colorBucket1);
            }
        );
    }



    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator ()
    {
        return new \ArrayIterator($this->histogram);
    }



    /**
     * Returns the first color
     *
     * @return Color
     */
    public function getDominantColor ()
    {
        return $this->histogram[0]->getRepresentativeColor();
    }



    /**
     * Returns the next dominant color
     *
     * @param Color $excludeColor
     * @param float $similarity
     *
     * @return Color|null
     */
    public function getColors (Color $excludeColor, $similarity)
    {
        $colors = array();
        foreach ($this->histogram as $colorBucket)
        {
            if ($similarity > $excludeColor->getColorDifference($colorBucket->getRepresentativeColor()))
            {
                continue;
            }

            $colors[] = $colorBucket->getRepresentativeColor();
        }

        return $colors;
    }
}