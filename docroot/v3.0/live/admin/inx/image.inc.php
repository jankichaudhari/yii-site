<?php
// image functions

$logo = '<img src="/images/sys/admin/logo.gif" id="logoprint">';

/*
naming of thumbnail images
rendering icons and standard images

defaults for phpthumb functions etc

*/

// render an icon from the icons folder
// options: tooltip, rollover, alt, link, disabled (greyed out, if image available (suffix = _disabled))
// get image size using php function get_image_size, or use default hxw of 16x16?
function icon($_data = array())
{

	foreach ($_data as $key=> $val) {

	}
}

/*
process a photo. take the original uploaded file and create all required variations
only works with deal images, requires a dea_id
600x600 is original, and used in slodeshows (toobig?)
400x400 for main web image
200x200 for other shots
146x146 for home page and production page
56x56 for thumbnail (search results etc)
*/
$thumbnail_sizes = array(
	'600x600'=> 'full',
	'400x400'=> 'large',
	'200x200'=> 'small',
	'146x146'=> 'thumb1',
	'56x56'  => 'thumb2'
);

function processPhoto($photo, $dea_id)
{

	require_once("inx/phpThumb/phpthumb.class.php");

	$thumbnail_sizes = array(
		'600x600'=> 'full',
		'400x400'=> 'large',
		'200x200'=> 'small',
		'146x146'=> 'thumb1',
		'56x56'  => 'thumb2'
	);

	$image_path_property = IMAGE_PATH_PROPERTY . $dea_id . "/";
	$image_url_property  = IMAGE_URL_PROPERTY . $dea_id . "/";
	//$image_path_property = "/var/www/ws-images/p/".$dea_id."/";

// print($image_path_property.$photo);
// exit;
	foreach ($thumbnail_sizes as $dims=> $ext) {
		$dim    = explode('x', $dims);
		$width  = $dim[0];
		$height = $dim[1];

		$attributes = getimagesize($image_path_property . $photo);
		// only process images that are big enough
		if ($attributes[0] >= $width && $attributes[1] >= $height) {

			$phpThumb = new phpThumb();
			// set data
			$phpThumb->setSourceFilename($image_path_property . $photo);
			$phpThumb->setParameter('h', $height);
			$phpThumb->setParameter('w', $width);
			$phpThumb->setParameter('config_output_format', 'jpg');
			$phpThumb->setParameter('config_allow_src_above_docroot', true);

			// generate & output thumbnail
			$output_filename = $image_path_property . str_replace('.jpg', '', $photo) . '_' . $ext . '.' . $phpThumb->config_output_format;
			if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
				$output_size_x = ImageSX($phpThumb->gdimg_output);
				$output_size_y = ImageSY($phpThumb->gdimg_output);
				if ($output_filename) {
					if ($phpThumb->RenderToFile($output_filename)) {
						// do something on success
						#echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
					} else {
						// do something with debug/error messages
						echo 'Failed (size=' . $thumbnail_width . '):<pre>' . implode("\n\n", $phpThumb->debugmessages) . '</pre>';
					}
				} else {
					$phpThumb->OutputThumbnail();
				}
			} else {
				// do something with debug/error messages
				$errors .= '<p>Failed (size=' . $thumbnail_width . ').<br>
				<div style="background-color:#FFEEDD; font-weight: bold; padding: 10px;">' . $phpThumb->fatalerror . '</div>
				<form><textarea rows="10" cols="60" wrap="off">' . htmlentities(implode("\n* ", $phpThumb->debugmessages)) . '</textarea></form></p><hr>';
			}

		}
	}

	if ($errors) {
		echo $errors;
		exit;
	}

}

// create epc images and thumbnails
function processEPC($photo, $dea_id)
{
	require_once(dirname(__FILE__) . "/phpThumb/phpthumb.class.php");
	$errors = '';

	$thumbnail_sizes = array(
		'full'   => array('h' => '600'),
		'large'  => array('h' => '600'),
		'small'  => array('w' => '200'),
		'thumb1' => array('w' => '146',
						  'h' => '146'),
		'original' => array('h' => '', 'w' => ''),
	);

	$image_path_property = IMAGE_PATH_PROPERTY . $dea_id . "/";
	$image_url_property  = IMAGE_URL_PROPERTY . $dea_id . "/";
	$attributes          = getimagesize($image_path_property . $photo);
	foreach ($thumbnail_sizes as $ext => $dims) {

		if (isset($dims['w']) && $dims['w']) {
			$dims['w'] = $attributes[0] < $dims['w'] ? $attributes[0] : $dims['w'];
		}

		if (isset($dims['h']) && $dims['h']) {
			$dims['h'] = $attributes[1] < $dims['h'] ? $attributes[1] : $dims['h'];
		}

		// only process images that are big enough

		$phpThumb = new phpThumb();
		// set data
		$phpThumb->setSourceFilename($image_path_property . $photo);
		if (isset($dims['w']) && $dims['w']) {
			$phpThumb->setParameter('w', $dims['w']);
		}
		if (isset($dims['h']) && $dims['h']) {
			$phpThumb->setParameter('h', $dims['h']);
		}
		$phpThumb->setParameter('config_output_format', 'gif');
		$phpThumb->setParameter('config_allow_src_above_docroot', true);
		// generate & output thumbnail
		$output_filename = $image_path_property . str_replace('.gif', '', $photo) . '_' . $ext . '.' . $phpThumb->config_output_format;
		if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
			$output_size_x = ImageSX($phpThumb->gdimg_output);
			$output_size_y = ImageSY($phpThumb->gdimg_output);
			if ($output_filename) {
				if ($phpThumb->RenderToFile($output_filename)) {
					// do something on success
					#echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
				} else {
					// do something with debug/error messages
					echo 'Failed (size=' . $thumbnail_width . '):<pre>' . implode("\n\n", $phpThumb->debugmessages) . '</pre>';
				}
			} else {
				$phpThumb->OutputThumbnail();
			}
		} else {
			// do something with debug/error messages
			$errors .= '<p>Failed (size=' . $thumbnail_width . ').<br>
				<div style="background-color:#FFEEDD; font-weight: bold; padding: 10px;">' . $phpThumb->fatalerror . '</div>
				<form><textarea rows="10" cols="60" wrap="off">' . htmlentities(implode("\n* ", $phpThumb->debugmessages)) . '</textarea></form></p><hr>';
		}

	}

	if ($errors) {
		echo $errors;
		exit;
	}

}

function CopyFiles($source, $dest)
{
	$folder = opendir($source);
	while ($file = readdir($folder)) {
		if ($file == '.' || $file == '..') {
			continue;
		}

		if (is_dir($source . '/' . $file)) {
			mkdir($dest . '/' . $file, 0777);
			CopySourceFiles($source . '/' . $file, $dest . '/' . $file);
		} else {
			copy($source . '/' . $file, $dest . '/' . $file);
		}

	}
	closedir($folder);
	return 1;
}

?>