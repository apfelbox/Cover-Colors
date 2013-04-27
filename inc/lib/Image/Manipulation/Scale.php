<?php

namespace Image\Manipulation;


use Image\Image;

class Scale
{
    /**
     * @var \Image\Image
     */
    private $image;



    /**
     * @param Image $image
     */
    public function __construct (Image $image)
    {
        $this->image = $image;
    }



    /**
     * Scales the image to maximum dimensions
     *
     * @param int $width
     * @param int $height
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function scaleToMaximumDimensions ($width, $height)
    {
        if (!self::isValidImageDimension($width) || !self::isValidImageDimension($height))
        {
            throw new \InvalidArgumentException('Width and Height must be integer > 0.');
        }

        $heightFactor = $this->image->getHeight() / $height;
        $widthFactor = $this->image->getWidth() / $width;

        if ($widthFactor < $heightFactor)
        {
            $this->scaleToHeight($height);
        }
        else
        {
            $this->scaleToWidth($width);
        }
    }



    /**
     * Scales the image proportionally to a height
     *
     * @param int $height
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function scaleToHeight ($height)
    {
        if (!self::isValidImageDimension($height))
        {
            throw new \InvalidArgumentException('Width and Height must be integer > 0.');
        }
        $width = (int) round(($height / $this->image->getHeight()) * $this->image->getWidth());
        $this->image->scaleToDimensions($width, $height);
    }



    /**
     * Scales the image proportionally to a width
     *
     * @param int $width
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function scaleToWidth ($width)
    {
        if (!self::isValidImageDimension($width))
        {
            throw new \InvalidArgumentException('Width and Height must be integer > 0.');
        }

        $height = (int) round(($width / $this->image->getWidth()) * $this->image->getHeight());
        $this->image->scaleToDimensions($width, $height);
    }



    /**
     * Returns, if the size is a valid image dimension
     * @static
     *
     * @param int $size
     *
     * @return bool
     */
    public static function isValidImageDimension ($size)
    {
        if (!is_int($size) && !is_float($size) && !ctype_digit($size))
        {
            return false;
        }

        return 0 < (int) $size;
    }
}