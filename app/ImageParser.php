<?php

class ImageParser
{

    public $path = 'temp/';
    public $filepath;
    public $new_filepath;
    public $new_file;
    public $original_image_data;
    public $original_size_data;
    public $input_width;
    public $input_height;
    public $new_width;
    public $new_height;
    public $nr_palette_colors = 15;

    /**
     * Instanciate an object creating the temporary
     * name for the original pic and it's correspondant
     * section of the pic.
     * 
     */
    function __construct()
    {
        $this->filepath = $this->path . $this->random(5) . '_o.jpeg';
        $this->new_filepath = $this->path . $this->random(5) . '_n.jpeg';
    }

    /**
     * Download the picture from the source $dir
     * and creates a new picture extracting a section
     * from the original.
     * 
     * The section will be extracted from the center,
     * where the object is supposed to be located.
     *
     * @param string $dir
     * @return void
     */
    public function load($dir)
    {
        if (!file_put_contents($this->filepath, file_get_contents($dir))) {
            Log::add("Error getting the picture form the source: $dir");
        }

        $this->original_image_data = imagecreatefromjpeg($this->filepath);
        $this->original_size_data = getimagesize($this->filepath);
        $this->input_width = $this->original_size_data[0];
        $this->input_height = $this->original_size_data[1];
        $this->new_width = ($this->input_width) / 100 * 60;
        $this->new_width_coord = ($this->input_width) / 100 * 20;
        $this->new_height = ($this->input_height) / 100 * 20;
        $this->new_height_coord = ($this->input_height) / 100 * 40;
    }

    /**
     * Will analize the new picture (section)
     * extracting the average color calculated from
     * the $nr_palette_colors color mostly used
     * in the section.
     *
     * @return string
     */
    public function parse()
    {
        $this->new_file = ImageCreateTrueColor($this->new_width, $this->new_height);
        imagecopyresampled($this->new_file, $this->original_image_data, 0, 0, $this->new_width_coord, $this->new_height_coord, $this->new_width, $this->new_height, $this->new_width, $this->new_height);
        imagejpeg($this->new_file, $this->new_filepath);

        return $this->extractColor();
    }

    /**
     * Generates a randome string with
     * the lenght specified as a parameter
     *
     * @param integer $length
     * @return void
     */
    public function random($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Analize the picture's section pixel by pixel
     * generating an average color.
     *
     * @return string
     */
    public function extractColor()
    {
        $w = imagesx($this->new_file);
        $h = imagesy($this->new_file);
        $r = $g = $b = 0;
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($this->new_file, $x, $y);
                $r += $rgb >> 16;
                $g += $rgb >> 8 & 255;
                $b += $rgb & 255;
            }
        }
        $pxls = $w * $h;
        $r = dechex(round($r / $pxls));
        $g = dechex(round($g / $pxls));
        $b = dechex(round($b / $pxls));
        if (strlen($r) < 2) {
            $r = 0 . $r;
        }
        if (strlen($g) < 2) {
            $g = 0 . $g;
        }
        if (strlen($b) < 2) {
            $b = 0 . $b;
        }
        return "#" . $r . $g . $b;
    }

    /**
     * Remove the temporarily created pictures
     *
     * @return void
     */
    public function erasePics()
    {
        if (unlink($this->filepath) && unlink($this->new_filepath)) {
            return true;
        }

        return false;
    }
}
