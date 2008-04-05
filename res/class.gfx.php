<?php

###############################################################
#
# Class: GFX
# Author: Neo Geek (NG)
#
###############################################################

class GFX
{

	public function Resize($image, $width = 100, $height = 100, $output = null) {

		if (!is_file($image)) { return false; }

		$properties = getimagesize($image);

		$cache = md5(serialize(func_get_args()) . serialize($properties)) . '.' . substr($properties['mime'], 6);

		if (is_dir($output) && is_file($output . $cache)) { return $output . $cache; }

		if ($properties['mime'] == 'image/jpeg') { $original = imagecreatefromjpeg($image); }
		else if ($properties['mime'] == 'image/gif') { $original = imagecreatefromgif($image); }
		else if ($properties['mime'] == 'image/png') { $original = imagecreatefrompng($image); }

		$new = imagecreatetruecolor($width, $height);

		$ratio = $properties[0]/$properties[1];

		if ($width/$height < $ratio) { $width = $height*$ratio; } else { $height = $width/$ratio; }

		$offset_x = ($width-func_get_arg(1)) / 2;
		$offset_y = ($height-func_get_arg(2)) / 2;

		imagecopyresampled($new, $original, -$offset_x, -$offset_y, 0, 0, $width, $height, $properties[0], $properties[1]);

		if (!$output) { header('Content-type: ' . $properties['mime']); } else if (is_dir($output)) { $output .= $cache; }

		if ($properties['mime'] == 'image/jpeg') { imagejpeg($new, $output, 100); }
		else if ($properties['mime'] == 'image/gif') { imagegif($new, $output); }
		else if ($properties['mime'] == 'image/png') { imagepng($new, $output); }

		return $output;

	}

}

###############################################################

$GFX = new GFX;

###############################################################

?>