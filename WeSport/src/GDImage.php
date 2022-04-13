<?php

declare(strict_types=1);

/**
 * Class GDImageException.
 */
class GDImageException extends Exception
{
}

/**
 * @brief Class to use GD functions in an object oriented way.
 *
 * Each GD function imageXYZ($resource, ...) is mapped to $this->XYZ(...)
 * through __call() as $resource is a property of GDImage
 *
 * @see https://www.php.net/manual/fr/ref.image.php
 *
 * @author Jérôme Cutrona
 * @method affine()
 * @method affineMatrixConcat()
 * @method affineMatrixGet()
 * @method alphaBlending()
 * @method antiAlias()
 * @method arc()
 * @method bmp()
 * @method char()
 * @method charUp()
 * @method colorAllocate()
 * @method colorAllocateAlpha()
 * @method colorAt()
 * @method colorClosest()
 * @method colorClosestAlpha()
 * @method colorClosestHwb()
 * @method colorDeallocate()
 * @method colorExact()
 * @method colorExactAlpha()
 * @method colorMatch()
 * @method colorResolve()
 * @method colorResolveAlpha()
 * @method colorSet()
 * @method colorsForIndex()
 * @method colorsTotal()
 * @method colorTransparent()
 * @method convolution()
 * @method copy()
 * @method copyMerge()
 * @method copyMergeGray()
 * @method copyResampled()
 * @method copyResized()
 * @method create()
 * @method createFromBmp()
 * @method createFromGd2()
 * @method createFromGd2Part()
 * @method createFromGd()
 * @method createFromGif()
 * @method createFromJpeg()
 * @method createFromPng()
 * @method createFromWbmp()
 * @method createFromWebp()
 * @method createFromXbm()
 * @method createFromXpm()
 * @method createTrueColor()
 * @method crop()
 * @method cropAuto()
 * @method dashedLine()
 * @method destroy()
 * @method ellipse()
 * @method fill()
 * @method filledArc()
 * @method filledEllipse()
 * @method filledPolygon()
 * @method filledRectangle()
 * @method fillToBorder()
 * @method filter()
 * @method flip()
 * @method fontHeight()
 * @method fontHidth()
 * @method ftBBox()
 * @method ftText()
 * @method gammaCorrect()
 * @method gd2()
 * @method gd()
 * @method getClip()
 * @method getInterpolation()
 * @method gif()
 * @method grabScreen()
 * @method grabWindow()
 * @method interlace()
 * @method isTrueColor()
 * @method jpeg()
 * @method layerEffect()
 * @method line()
 * @method loadFont()
 * @method openPolygon()
 * @method paletteCopy()
 * @method paletteToTrueColor()
 * @method png()
 * @method polygon()
 * @method rectangle()
 * @method resolution()
 * @method rotate()
 * @method saveAlpha()
 * @method scale()
 * @method setBrush()
 * @method setClip()
 * @method setInterpolation()
 * @method setPixel()
 * @method setStyle()
 * @method setThickness()
 * @method setTile()
 * @method string()
 * @method stringUp()
 * @method sx()
 * @method sy()
 * @method trueColorToPalette()
 * @method ttfBBox()
 * @method ttfText()
 * @method types()
 * @method wbmp()
 * @method webp()
 * @method xbm()
 */
class GDImage
{
    const GD = 'gd';
    const GD2PART = 'gd2part';
    const GD2 = 'gd2';
    const GIF = 'gif';
    const JPEG = 'jpeg';
    const PNG = 'png';
    const WBMP = 'wbmp';
    const XBM = 'xbm';
    const XPM = 'xpm';

    /**
     * Factory known types.
     */
    private static array $factory_types = [
        self::GD,
        self::GD2PART,
        self::GD2,
        self::GIF,
        self::JPEG,
        self::PNG,
        self::WBMP,
        self::XBM,
        self::XPM,
    ];

    /**
     * Image identifier.
     *
     * @var resource
     */
    private $resource = null;

    /**
     * Disabled constructor.
     */
    private function __construct()
    {
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        if (!is_null($this->resource)) {
            imagedestroy($this->resource);
        }
    }

    /**
     * Factory to create a GDImage instance from width and height parameters.
     *
     * @param int  $x         Image width
     * @param int  $y         Image height
     * @param bool $truecolor creates a true color image if true and a palette based image otherwise
     *
     * @return GDImage
     *
     * @throws GDImageException
     */
    public static function createFromSize(int $x, int $y, bool $truecolor = true): self
    {
        $x = (int) $x;
        $y = (int) $y;
        if ($truecolor) {
            $resource = @imagecreatetruecolor($x, $y);
        } else {
            $resource = @imagecreate($x, $y);
        }
        if (false !== $resource) {
            $image = new self();
            $image->resource = $resource;

            return $image;
        } else {
            throw new GDImageException('Failed to create GD resource');
        }
    }

    /**
     * Factory to create a GDImage instance from filename and filetype paramters.
     *
     * @param string $filename name of the file
     * @param string $filetype type of the file (must be an element of self::$_factory_types)
     *
     * @return GDImage
     *
     * @throws GDImageException
     */
    public static function createFromFile(string $filename, string $filetype): self
    {
        if (!is_file($filename)) {
            throw new GDImageException("{$filename} : no such file");
        }
        if (!in_array($filetype, self::$factory_types)) {
            throw new GDImageException('unknown filetype');
        }
        $functionName = "imageCreateFrom{$filetype}";
        $image = new self();
        if (($tmp = @$functionName($filename)) === false) {
            throw new GDImageException("unable to load file '{$filename}'");
        }
        $image->resource = $tmp;

        return $image;
    }

    /**
     * Factory to create a GDImage instance from filename and filetype parameters.
     *
     * @return GDImage
     *
     * @throws GDImageException
     */
    public static function createFromString(string $data): self
    {
        if (($tmp = imagecreatefromstring($data)) !== false) {
            $image = new self();
            $image->resource = $tmp;

            return $image;
        } else {
            throw new GDImageException('unable to load data');
        }
    }

    /**
     * Trap "inaccessible methods" to invoke GD functions, if available.
     * If a method named 'colorAllocate' is trapped, it will try to invoke 'imageColorAllocate' function.
     *
     * @param string $methodName      name of the "inaccessible method"
     * @param array  $methodArguments array of the arguments of the "inaccessible method"
     *
     * @return mixed
     */
    public function __call(string $methodName, array $methodArguments)
    {
        $gdFunction = "image{$methodName}";
        if (!function_exists($gdFunction)) {
            throw new BadMethodCallException('Unknown method call: '.get_class($this)."::{$methodName}");
        }
        // Prevent direct call of imageCreateFrom...
        if (mb_eregi('^imageCreateFrom', $gdFunction)) {
            throw new BadMethodCallException('Forbidden method call '.get_class($this)."::{$methodName}");
        }
        // Special case of copy functions
        if (mb_eregi('^(copy|colormatch)', $methodName)) {
            // First parameter of the method should be an instance of the src
            if (isset($methodArguments[0]) && $methodArguments[0] instanceof self) {
                // Preparing argument for GD function call
                $methodArguments[0] = $methodArguments[0]->resource;
            } else {
                throw new InvalidArgumentException("First parameter of '".get_class($this)."::{$methodName}' should be an instance of ".get_class($this));
            }
        }
        // Avoid function which first parameter is not an image resource
        if (!mb_eregi('^image(font|ftbbox|grab|grab|loadfont|ps|types)', $gdFunction)) {
            // First parameter should be the image resource
            array_unshift($methodArguments, $this->resource);
        }
        // Call GD function
        $returnValue = @call_user_func_array($gdFunction, $methodArguments);
        if (null === $returnValue) {
            throw new BadMethodCallException('Error in '.get_class($this)."::{$methodName}");
        }
        if (is_resource($returnValue)) {
            $newImage = new GDImage();
            $newImage->resource = $returnValue;

            return $newImage;
        } else {
            return $returnValue;
        }
    }

    /**
     * Retrieve information about the currently installed GD library.
     *
     * @return array returns an associative array
     */
    public static function info(): array
    {
        return gd_info();
    }

    /**
     * Get the size of an image.
     *
     * @param string $filename  this parameter specifies the file you wish to retrieve information about
     * @param array  $imageInfo this optional parameter allows you to extract some extended information from the image file
     *
     * @return array an array with up to 7 elements. Not all image types will include the channels and bits elements. Index 0 and 1 contains respectively the width and the height of the image.
     */
    public static function getImageSize(string $filename, array &$imageInfo = []): array
    {
        return @getimagesize($filename, $imageInfo);
    }

    /**
     * Trap "inaccessible static methods" to invoke GD functions, if available.
     * If a method named 'colorAllocate' is trapped, it will try to invoke 'imageColorAllocate' function.
     *
     * @param string $methodName      name of the "inaccessible method"
     * @param array  $methodArguments array of the arguments of the "inaccessible method"
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $methodName, array $methodArguments)
    {
        $gdFunction = !function_exists($methodName) ? "image{$methodName}" : $methodName;
        if (function_exists($gdFunction) && !mb_eregi('^imageCreateFrom', $gdFunction)) {
            $returnValue = call_user_func_array($gdFunction, $methodArguments);
            if (null !== $returnValue) {
                return $returnValue;
            } else {
                throw new BadMethodCallException('Error in '.get_class()."::{$methodName}");
            }
        } else {
            throw new BadMethodCallException('Call to unknown static method '.get_class()."::{$methodName}");
        }
    }

    /**
     * Clone.
     *
     * @throws GDImageException
     */
    public function __clone()
    {
        if (!imageistruecolor($this->resource)) {
            if (($tmp = @imagecreate(imagesx($this->resource), imagesy($this->resource))) === false) {
                throw new GDImageException('unable to clone GDImage');
            }
            imagepalettecopy($tmp, $this->resource);
        } else {
            if (($tmp = @imagecreatetruecolor(imagesx($this->resource), imagesy($this->resource))) === false) {
                throw new GDImageException('unable to clone GDImage');
            }
        }
        imagecopy($tmp, $this->resource, 0, 0, 0, 0, imagesx($this->resource), imagesy($this->resource));
        $this->resource = $tmp;
    }
}
