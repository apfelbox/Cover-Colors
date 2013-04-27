<?php

use Image\Histogram\ColorHistogram;
use Color\Color;
use Image\Manipulation\Scale;

class Analyzer
{
    /**
     * @var Image\Image
     */
    private $image;


    /**
     * @var
     */
    private $histogram;

    const MAX_WIDTH_ANALYZED_IMAGE  = 36;
    const MAX_HEIGHT_ANALYZED_IMAGE = 36;


    /**
     * @param \Image\Image $image
     */
    public function __construct (\Image\Image $image)
    {
        $this->image = $image;
        $this->histogram = new ColorHistogram(.1);

        $this->analyze();
    }



    /**
     * Analyzes the image and prepares the result
     */
    private function analyze ()
    {
        $this->scaleImageToAnalyzeDimensions();
        $this->filterImage();
        $this->fillHistogram();
        $this->histogram->sortAndFilter(2);
    }



    /**
     * Scales the image to the maximum dimensions
     */
    private function scaleImageToAnalyzeDimensions ()
    {
        $scale = new Scale($this->image);
        $scale->scaleToMaximumDimensions(self::MAX_WIDTH_ANALYZED_IMAGE, self::MAX_HEIGHT_ANALYZED_IMAGE);
    }


    /**
     * The image is filtered using a gaussian filter, to reduce the details
     */
    private function filterImage ($iterations = 2)
    {
        for ($i = 0; $i < $iterations; $i++)
        {
            $this->image->filterImage(IMG_FILTER_GAUSSIAN_BLUR);
        }
    }


    /**
     * Returns a list of all color buckets
     */
    private function fillHistogram ()
    {
        for ($x = 0; $x < $this->image->getWidth(); $x++)
        {
            for ($y = 0; $y < $this->image->getHeight(); $y++)
            {
                $this->histogram->insertColor( $this->image->getColorAt($x, $y) );
            }
        }
    }



    /**
     * Returns the result of the image analysis
     *
     * @return array
     */
    public function getResult ()
    {
        $backgroundColor = $this->getBackgroundColor();
        $otherDominantColors = $this->histogram->getColors($backgroundColor, .2);

        // if there are not enough colors, just use black or white as replacement
        $replacementColor = $backgroundColor->isDarkColor() ? new Color(1., 1., 1.) : new Color(0., 0., 0.);
        $title = isset($otherDominantColors[1]) ? $otherDominantColors[1] : $replacementColor;
        $songs = isset($otherDominantColors[2]) ? $otherDominantColors[2] : $replacementColor;


        return (object) array(
            "background" => $backgroundColor,
            "title" => $title,
            "songs" => $songs
        );
    }


    /**
     * Returns the background color
     *
     * @return Color
     */
    public function getBackgroundColor ()
    {
        return $this->getBorderHistogram()->getDominantColor();
    }



    /**
     * Returns the histogram for the border
     *
     * @return ColorHistogram
     */
    private function getBorderHistogram ()
    {
        $borderHistogram = new ColorHistogram(.1);

        // insert top border
        $y = 0;
        for ($x = 0; $x < $this->image->getWidth(); $x++)
        {
            $borderHistogram->insertColor( $this->image->getColorAt($x, $y) );
        }

        // insert bottom border
        $y = $this->image->getHeight() - 1;
        for ($x = 0; $x < $this->image->getWidth(); $x++)
        {
            $borderHistogram->insertColor( $this->image->getColorAt($x, $y) );
        }

        // insert left border (but exclude bottom and top value - they are in the top and bottom border)
        $x = 0;
        for ($y = 1; $y < $this->image->getHeight() - 1; $y++)
        {
            $borderHistogram->insertColor( $this->image->getColorAt($x, $y) );
        }

        // insert right border (but exclude bottom and top value - they are in the top and bottom border)
        $x = $this->image->getWidth() - 1;
        for ($y = 1; $y < $this->image->getHeight() - 1; $y++)
        {
            $borderHistogram->insertColor( $this->image->getColorAt($x, $y) );
        }

        return $borderHistogram;
    }
}