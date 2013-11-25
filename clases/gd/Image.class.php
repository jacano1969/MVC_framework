<?php
/**
 * This file is part of MVC framework
 *
 * MVC framework is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * MVC framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MVC framework; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @author $Author$
 * @version $Rev: 16464 $
 * @updated $Date$
 *
 * @copyright The MVC framework Team <lucasrsp@gmail.com> http://MVC framework.sf.net
 */

namespace gd;

use core\Object;
use io\File;
use io\FileNotFoundException;

/**
 * General purpose Image class.
 */
class Image extends Object
{

    const KB = 1024;
    const MB = 1048576;

    /**
     * Image GD Resource
     */
    protected $image = null;

    /**
     * Image Data
     */
    protected $data = null;

    /**
     * Image Type
     */
    protected $type = null;

    private $width = null;
    private $height = null;

    /**
     * Instantiates a new image object for the supplied data and type
     *
     * @param string $data
     * @param int $type
     * @param boolean $parse (should we load an image resource from this data?, default:true. Mandatory for resampling)
     * @param int $width (in case created in non-parsing mode, specify width)
     * @param int $height (in case created in non-parsing mode, specify height)
     */
    public function __construct($data, $type, $parse = true, $width = null, $height = null) {
        if ($parse) {
            $this->image = @imageCreateFromString($data);
            if ($this->image === false) {
                $err = error_get_last();
                throw new ImageException(sprintf('Could not load image. Unsupported image type, or not an image: %s', $err['message']));
            }
        }
        else {
            $this->width = $width;
            $this->height = $height;
        }
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Loads an image from the supplied file
     *
     * @param File $file
     * @param $parse (parse loaded image, see constructor for more details)
     * @throws ImageException
     */
    public static function loadFromFile(File $file, $parse = true) {
        if (!$file->exists() || !$file->isReadable()) {
            throw new FileNotFoundException('Could not create image. File not found or not readable: "' . $file . '"');
        }
        $info = getimagesize($file->getName());
        $width = $info[0];
        $height = $info[1];
        return new static($file->read(), $info[2], $parse, $width, $height);
    }

    /**
     * Loads an image from the supplied data
     *
     * @param string $data
     * @param string mimeType
     * @throws ImageException
     */
    public static function loadFromString($data, $mimeType) {
        $types['image/gif'] = GD::TYPE_GIF;
        $types['image/jpeg'] = GD::TYPE_JPEG;
        $types['image/png'] = GD::TYPE_PNG;
        $types['application/x-shockwave-flash'] = GD::TYPE_SWF;

        if (!isset($types[$mimeType])) {
            throw new Exception("Unsupported mime type $mimeType");
        }

        return new static($data, $types[$mimeType]);
    }


    /**
     * Gets the image width
     *
     * @return int
     */
    public function getWidth() {
        if ($this->width !== null) return $this->width;
        else return imagesx($this->image);
    }

    /**
     * Gets the image width/height ratio
     *
     * @return float
     */
    public function getRatio() {
        return round($this->getWidth() / $this->getHeight(), 2);
    }

    /**
     * Gets the image height
     *
     * @return int
     */
    public function getHeight() {
        if($this->height!==null) return $this->height;
        else return imagesy($this->image);
    }

    public function crop( $left, $top, $width, $height ) {
        $this->checkResource();
        $new = imageCreateTrueColor( $width, $height );
        imageCopyResampled( $new, $this->image, 0, 0, $left, $top, $width, $height, $width, $height );
        $this->image = $new;
        $this->data = null;
    }

    /**
     * Resamples the image to the supplied width / height
     *
     * @param int $width
     * @param int $height
     */
    public function resample($width, $height) {
        $this->checkResource();
        $currHeight = $this->getHeight();
        $currWidth = $this->getWidth();
        $sampleH = $currHeight * $width / $currWidth;
        $sampleW = $currWidth * $height / $currHeight;
        if ($width > $sampleW) $width = $sampleW;
        if ($height > $sampleH) $height = $sampleH;

        $new = imageCreateTrueColor($width, $height);
        imageCopyResampled($new, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new;
        $this->data = null;
    }


    /**
     * Resamples the image to the supplied width / height
     *
     * @param int $width
     * @param int $height
     */
    public function fillAndCrop($width, $height) {
        $this->checkResource();
        $requestWidth  = $width;
        $requestHeight = $height;
        $currHeight = $this->getHeight();
        $currWidth = $this->getWidth();

        //create an image of at lease $width and $height;
        $sampleH = $currHeight * $width / $currWidth;
        $sampleW = $currWidth * $height / $currHeight;
        if ($sampleW > $width) $width = $sampleW;
        if ($sampleH > $height) $height = $sampleH;

        $bigger = imageCreateTrueColor($width, $height);
        imageCopyResampled($bigger, $this->image, 0, 0
                            , 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

        $new = imageCreateTrueColor($requestWidth, $requestHeight);
        imageCopyResampled($new, $bigger, 0, 0
                            , 0, 0, $requestWidth, $requestHeight, $requestWidth, $requestHeight );

        $this->image = $new;
        $this->data = null;
        //return $this->resample($width, $height);
    }

    /**
     * Resamples the image by the supplied width/height ratio, automatically calculating the new width/height
     *
     * @param float $ratio
     */
    public function resampleRatio($ratio) {
        $this->resample($this->getWidth() * $ratio, $this->getHeight() * $ratio);
    }

    /**
     * Resamples the image to the supplied width, automatically calculating the height, according to ratio.
     *
     * @param int $width
     */
    public function resampleToWidth($width) {
        $h = $this->getHeight() * $width / $this->getWidth();
        $w = $width;
        $this->resample($w, $h);
    }

    /**
     * Resamples the image to the supplied height, automatically calculating the width, according to ratio.
     *
     * @param int $height
     *
     */
    public function resampleToHeight($height) {
        $w = $this->getWidth() * $height / $this->getHeight();
        $h = $height;
        $this->resample($w, $h);
    }

    /**
     * Resamples the image to a ratio calculated to whatever enlarges the image the most of the supplied width/height
     *
     * @param int $width
     * @param int $height
     */
    public function resampleToMaxSize($width, $height) {
        if ($this->getWidth() > $width || $this->getHeight() > $height) {
            $wRatio = $width / $this->getWidth();
            $hRatio = $height / $this->getHeight();

            $ratio = $wRatio > $hRatio ? $hRatio : $wRatio;
            $this->resampleRatio($ratio);
        }
    }

    /**
     * Saves the image to file with optional quality
     *
     * @param string $filename
     * @param int $quality
     */
    public function save($filename, $quality = 0) {
        switch ($this->type) {
            case GD::TYPE_PNG:
            case IMAGETYPE_PNG: // Workaround for stupid GD/imginfo constants being different...
                imagePng($this->image, $filename, $quality);
                break;
            case GD::TYPE_JPEG:
                imageJpeg($this->image, $filename, $quality);
                break;
            case GD::TYPE_GIF:
                imageGif($this->image, $filename);
                break;
        }
    }

    /**
     * Gets the image size in bytes
     *
     * @return int
     */
    public function getSize($format = false) {
        $size = strlen($this->getData());
        if ($format) {
            if ($size > self::MB) {
                return sprintf('%1.2f MB', $size / self::MB);
            } else {
                return sprintf('%1.2f KB', $size / self::KB);
            }
        } else {
            return $size;
        }
    }

    /**
     * Gets the image type (One of the GD Constants)
     *
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Gets the image mime type
     *
     * @return string
     */
    public function getMimeType() {
        return image_type_to_mime_type($this->type);
    }

    /**
     * Outputs the image
     */
    public function output() {
        switch ($this->type) {
            case GD::TYPE_PNG:
                imagePng($this->image);
                break;
            case GD::TYPE_JPEG:
                imageJpeg($this->image);
                break;
            case GD::TYPE_GIF:
                imageGif($this->image);
                break;
            case GD::TYPE_SWF:
                echo "Cannot output SWF";
        }
    }

    /**
     * Gets the image data. This method buffers and returns the output
     *
     * @return binary data
     */
    public function getData() {
        if ($this->data === null) {
            ob_start();
            $this->output();
            $this->data = ob_get_contents();
            ob_end_clean();
        }
        return $this->data;
    }

    /**
     * Returns a string representation of this image
     *
     * @return string
     */
    public function __toString() {
        try {
            return sprintf('Image [%s] (%dx%d): %d bytes', $this->getMimeType(), $this->getWidth(), $this->getHeight(), 10);
        } catch (ImageException $e) {
            return sprintf("ERROR: %s", $e->getMessage());
        }
    }

    private function checkResource() {
        if (!$this->image) {
           throw new \Exception("Image resource not parsed (see constructor). Aborting operation");
        }
    }

}
