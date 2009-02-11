<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2008, Phoronix Media
	Copyright (C) 2008, Michael Larabel
	bilde_gd_renderer: An abstract class providing a GD library renderer that can then be extended by a PNG and JPEG renderer

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

abstract class bilde_gd_renderer extends bilde_renderer
{
	public function __construct($width, $height, $embed_identifiers = "")
	{
		$this->image = $this->init_new_gd_image($width, $height);
		$this->image_width = $width;
		$this->image_height = $height;
	}
	public static function renderer_supported()
	{
		return extension_loaded("gd");
	}

	/*
	public function render_image($output_file = null, $quality = 100)
	{
		// To be implemented by the class extending bilde_gd_renderer
	}
	*/

	public function resize_image($width, $height)
	{
		$img = $this->image;
		$this->image = $this->init_new_gd_image($width, $height);
		$this->image_width = $width;
		$this->image_height = $height;
		$this->image_copy_merge($img, 0, 0);
	}
	public function destroy_image()
	{
		imagedestroy($this->image);
	}

	public function write_text_left($text_string, $font_type, $font_size, $font_color, $bound_x1, $bound_y1, $bound_x2, $bound_y2, $rotate_text = false)
	{
		$text_dimensions = $this->text_string_dimensions($text_string, $font_type, $font_size);
		$text_width = $text_dimensions[0];
		$text_height = $text_dimensions[1];

		if($rotate_text == false)
		{
			$text_x = $bound_x1;
			$text_y = $bound_y1 + round($text_height / 2);
			$rotation = 0;
		}
		else
		{
			$text_x = $bound_x1 - round($text_height / 4);
			$text_y = $bound_y1 + round($text_height / 2);
			$rotation = 270;
		}
		imagettftext($this->image, $font_size, $rotation, $text_x, $text_y, $font_color, $font_type, $text_string);
	}
	public function write_text_right($text_string, $font_type, $font_size, $font_color, $bound_x1, $bound_y1, $bound_x2, $bound_y2, $rotate_text = false)
	{
		$text_dimensions = $this->text_string_dimensions($text_string, $font_type, $font_size);
		$text_width = $text_dimensions[0];
		$text_height = $text_dimensions[1];

		if($rotate_text == false)
		{
			$rotation = 0;
		}
		else
		{
			$rotation = 90;
		}

		$text_x = $bound_x2 - $text_width;
		$text_y = $bound_y1 + round($text_height / 2);

		imagettftext($this->image, $font_size, $rotation, $text_x, $text_y, $font_color, $font_type, $text_string);
	}
	public function write_text_center($text_string, $font_type, $font_size, $font_color, $bound_x1, $bound_y1, $bound_x2, $bound_y2, $rotate_text = false)
	{
		if($bound_x1 != $bound_x2)
		{
			while($this->text_string_width($this->trim_double($text_string, 2), $font_type, $font_size) > abs($bound_x2 - $bound_x1 - 3))
			{
				$font_size -= 0.5;
			}
		}

		$text_dimensions = $this->text_string_dimensions(strtoupper($text_string), $font_type, $font_size);
		$text_height = $text_dimensions[1];

		$text_dimensions = $this->text_string_dimensions($text_string, $font_type, $font_size);
		$text_width = $text_dimensions[0];

		if($rotate_text == false)
		{
			$rotation = 0;
			$text_x = (($bound_x2 - $bound_x1) / 2) + $bound_x1 - round($text_width / 2);
			$text_y = $bound_y1 + $text_height;
		}
		else
		{
			$rotation = 90;
			$text_x = $bound_x1 + $text_height;
			$text_y = (($bound_y2 - $bound_y1) / 2) + $bound_y1 + round($text_width / 2);
		}

		imagettftext($this->image, $font_size, $rotation, $text_x, $text_y, $font_color, $font_type, $text_string);
	}

	public function draw_rectangle($x1, $y1, $width, $height, $background_color)
	{
		imagefilledrectangle($this->image, $x1, $y1, $width, $height, $background_color);
	}
	public function draw_rectangle_border($x1, $y1, $width, $height, $border_color)
	{
		imagerectangle($this->image, $x1, $y1, $width, $height, $border_color);
	}
	public function draw_polygon($points, $body_color, $border_color = null, $border_width = 0)
	{
		$num_points = floor(count($points) / 2);

		imagefilledpolygon($this->image, $points, $num_points, $body_color);

		if($border_width > 0 && !empty($border_color))
		{
			// TODO: implement $border_width
			imagepolygon($this->image, $points, $num_points, $border_color);
		}
	}
	public function draw_ellipse($center_x, $center_y, $width, $height, $body_color, $border_color = null, $border_width = 0)
	{
		imagefilledellipse($this->image, $center_x, $center_y, $width, $height, $body_color);

		if($border_width > 0 && !empty($border_color))
		{
			// TODO: implement $border_width
			imageellipse($this->image, $center_x, $center_y, $width, $height, $border_color);
		}
	}
	public function draw_line($start_x, $start_y, $end_x, $end_y, $color, $line_width = 1)
	{
		for($i = 0; $i < $line_width; $i++)
		{
			if($start_x == $end_x)
			{
				imageline($this->image, $start_x + $i, $start_y, $end_x + $i, $end_y, $color);
			}
			else
			{
				imageline($this->image, $start_x, $start_y + $i, $end_x, $end_y + $i, $color);
			}
		}
	}

	public function png_image_to_type($file)
	{
		return imagecreatefrompng($file);
	}
	public function jpg_image_to_type($file)
	{
		return imagecreatefromjpeg($file);
	}
	public function image_copy_merge($source_image_object, $to_x, $to_y, $source_x = 0, $source_y = 0, $width = -1, $height = -1)
	{
		if($width == -1)
		{
			$width = imagesx($source_image_object);
		}
		if($height == -1)
		{
			$height = imagesy($source_image_object);
		}

		imagecopy($this->image, $source_image_object, $to_x, $to_y, $source_x, $source_y, $width, $height);
	}
	public function convert_hex_to_type($hex)
	{
		return imagecolorallocate($this->image, hexdec(substr($hex, 1, 2)), hexdec(substr($hex, 3, 2)), hexdec(substr($hex, 5, 2)));
	}
	public function text_string_dimensions($string, $font_type, $font_size, $predefined_string = false)
	{
		$box_array = imagettfbbox($font_size, 0, $font_type, $string);
		$box_width = $box_array[4] - $box_array[6];

		if($predefined_string)
		{
			$box_array = imagettfbbox($size, 0, $font, "AZ@![]()@|_");
		}

		$box_height = $box_array[1] - $box_array[7];

		return array($box_width, $box_height);
	}

	// Privates / Protected

	protected function init_new_gd_image($width, $height)
	{
		$img = imagecreate($width, $height);

		imageinterlace($img, true);

		if(function_exists("imageantialias"))
		{
			imageantialias($img, true);
		}

		return $img;
	}
}

?>
