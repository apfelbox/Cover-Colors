<?php

namespace Image;

use Color\Color;


class Image
{
    /**
     * @var resource
     */
    private $imageHandle;



    /**
     * @param string $imageContent the image contents
     */
    public function __construct ($imageContent)
    {
        $this->imageHandle = imagecreatefromstring($imageContent);
    }



    /**
     * Creates a new image object from a file on the hdd
     *
     * @param string $filePath
     *
     * @return Image
     */
    public static function createFromFile ($filePath)
    {
        return new self(file_get_contents($filePath));
    }



    /**
     * Creates a new image object from a data-url
     *
     * @param string $dataUrl
     *
     * @return Image
     * @throws \InvalidArgumentException
     */
    public static function createFromDataUrl ($dataUrl)
    {
        if (1 === preg_match("~^data\:(?P<type>[a-z\/]*?)(;charset=(?P<charset>.*?))?(;(?P<base64>base64))?,(?P<content>.*?)$~i", $dataUrl, $matches))
        {
            if (0 !== strpos($matches["type"], "image/"))
            {
                throw new \InvalidArgumentException("Only image data-urls are accepted.");
            }

            $content = $matches["content"];

            if (!empty($matches["base64"]))
            {
                $content = base64_decode($content);
            }

            return new self($content);
        }

        throw new \InvalidArgumentException("No valid data url given");
    }



    /**
     * Resizes the image to the new dimensions
     *
     * @param int $width
     * @param int $height
     */
    public function scaleToDimensions ($width, $height)
    {
        $newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled(
            $newImage, // $dst_image,
            $this->imageHandle, // $src_image
            0, // $dst_x
            0, // $dst_y
            0, // $src_x
            0, // $src_y
            $width, // $dst_w
            $height, // $dst_h
            $this->getWidth(), // $src_w
            $this->getHeight() // $src_h
        );

        $this->imageHandle = $newImage;
    }



    /**
     * Filters the image with the given filter
     *
     * @param int $filterType one of the IMG_FILTER_* constants
     */
    public function filterImage ($filterType)
    {
        imagefilter($this->imageHandle, $filterType);
    }



    /**
     * Returns the width of the image
     *
     * @return int
     */
    public function getWidth ()
    {
        return imagesx($this->imageHandle);
    }



    /**
     * Returns the height of the image
     *
     * @return int
     */
    public function getHeight ()
    {
        return imagesy($this->imageHandle);
    }



    /**
     * @param resource $imageHandle
     */
    public function setImageHandle ($imageHandle)
    {
        $this->imageHandle = $imageHandle;
    }



    /**
     * @return resource
     */
    public function getImageHandle ()
    {
        return $this->imageHandle;
    }



    /**
     * Returns the color at a given image position
     *
     * @param int $x
     * @param int $y
     *
     * @return Color
     */
    public function getColorAt ($x, $y)
    {
        $colorIndex = imagecolorat($this->imageHandle, $x, $y);
        $rgb = imagecolorsforindex($this->imageHandle, $colorIndex);

        $r = $rgb["red"]   / 255;
        $g = $rgb["green"] / 255;
        $b = $rgb["blue"]  / 255;

        return new Color($r, $g, $b);
    }



    /**
     * Returns the image content as string
     *
     * @return string
     */
    public function getImageDataUrl ()
    {
        ob_start();
        imagejpeg($this->imageHandle, null, 95);
        $imageContent = ob_get_clean();
        return "data:image/jpeg;base64," . base64_encode($imageContent);
    }
}