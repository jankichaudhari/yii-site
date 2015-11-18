<?php
namespace application\models\Place;
use \Yii as Yii;

/**
 * These images are displayed on details page.
 * small thumbnails of these images are displayed in a list
 * onclick large image pops up.
 *
 * it has four sizes:
 * thumb, small, medium and fourth which IS NOT main.
 * the latest one is saved as it is without any resizing.
 *
 * Class GalleryImage
 * @package application\models\Place
 */
final class GalleryImage extends Image
{
	const RECORD_TYPE = 'Place';

	public $thumbName = '';
	public $smallName = '';
	public $mediumName = '';

	protected $thumbSuffix = '_thumb';
	protected $smallSuffix = '_small';
	protected $mediumSuffix = '_medium';

	protected $resizeSizes = array(
		'_thumb'  => array(
			'w' => 75,
			'h' => 75
		),
		'_small'  => array(
			'w' => 100,
			'h' => 100
		),
		'_medium' => array(
			'w' => 640,
			'h' => 427
		),
		'_main'   => array(
			'w' => 1280,
			'h' => 1024
		)
	);

	protected $sizes = array('thumb', 'small', 'medium');

	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	protected function beforeSave()
	{

		/** @var $img \upload Tool */

		list($basename, $ext) = explode(".", $this->name);
		if (file_exists($this->fullPath . "/" . $this->name)) {
			$template     = $this->fullPath . "/" . $basename;
			$watermarkImg = Yii::app()->params['imgPath'] . '/watermark.png';

			foreach ($this->resizeSizes as $key => $sizes) {
				if (file_exists($template . $key . "." . $ext)) continue;
				$fullImageName = $this->fullPath . "/" . $this->name;
				switch ($key) {
					case '_thumb' :
						$img                     = $this->processThumb($fullImageName);
						$img->file_new_name_body = $basename . $key;
						break;
					case '_small' :
						$img                     = $this->processSmall($fullImageName);
						$img->file_new_name_body = $basename . $key;
						break;
					case '_medium' :
						$img                     = $this->processMedium($fullImageName);
						$img->file_new_name_body = $basename . $key;
						break;
					default :
						$img                           = Yii::app()->imagemod->load($fullImageName);
						$img->image_watermark          = $watermarkImg;
						$img->image_watermark_x        = 5;
						$img->image_watermark_y        = -5;
						$img->image_watermark_position = 'LB';
						$img->file_new_name_body       = $basename;
				}
				$img->file_overwrite    = true;
				$img->file_new_name_ext = $ext;
				$img->process($this->fullPath);
				if (!$img->processed) {
					$this->addError('file', 'cannot resize file to ' . $key . ' error ' . $img->error);
					return false;
				}
			}
		} else {
			$this->addError('file', 'File does not exist');
			return false;
		}
		return parent::beforeSave();
	}

	protected function beforeDelete()
	{

		$filePath = $this->fullPath;
		$orgFile  = $filePath . '/' . $this->name;
		if (file_exists($orgFile)) {
			unlink($orgFile);
		}
		$thumbFile = $filePath . '/' . $this->thumbName;
		if (file_exists($thumbFile)) {
			unlink($thumbFile);
		}
		$smallFile = $filePath . '/' . $this->smallName;
		if (file_exists($smallFile)) {
			unlink($smallFile);
		}
		$mediumFile = $filePath . '/' . $this->mediumName;
		if (file_exists($mediumFile)) {
			unlink($mediumFile);
		}
		return parent::beforeDelete();
	}

	private function processThumb($fullImageName)
	{

		/** @var $img \upload */
		$sizes = $this->resizeSizes['_thumb'];
		list($imgWidth, $imgHeight) = getimagesize($fullImageName);
		$img = Yii::app()->imagemod->load($fullImageName);
		if ($imgWidth > $sizes['w'] || $imgHeight > $sizes['h']) {
			$img->image_resize     = true;
			$img->image_ratio_crop = true;

			if (($imgWidth > $imgHeight)) { //horizontal image
				$img->image_y       = $sizes['h'];
				$img->image_ratio_x = true;
				$cropSize           = ($imgWidth * $sizes['h'] / $imgHeight - $sizes['w']) / 2 + 1;
				$img->image_crop    = array(0, $cropSize, 0, $cropSize);
			} else {
				$img->image_x       = $sizes['w'];
				$img->image_ratio_y = true;
				$cropSize           = ($imgHeight * $sizes['w'] / $imgWidth - $sizes['h']) / 2;
				$img->image_crop    = array($cropSize, 0, ($cropSize + 1), 0);
			}
		}
		return $img;
	}

	private function processSmall($fullImageName)
	{

		/** @var $img \upload */

		$sizes = $this->resizeSizes['_small'];
		list($imgWidth, $imgHeight) = getimagesize($fullImageName);
		$img = Yii::app()->imagemod->load($fullImageName);

		if ($imgWidth > $sizes['w'] || $imgHeight > $sizes['h']) {
			$img->image_resize = true;
			if (($imgWidth > $imgHeight)) { //horizontal image
				$img->image_y       = $sizes['h'];
				$img->image_ratio_x = true;
			} else {
				$img->image_x       = $sizes['w'];
				$img->image_ratio_y = true;
			}
		}

		return $img;
	}

	private function processMedium($fullImageName)
	{

		$sizes = $this->resizeSizes['_medium'];
		list($imgWidth, $imgHeight) = getimagesize($fullImageName);
		$img = Yii::app()->imagemod->load($fullImageName);
		if ($imgWidth > $sizes['w'] || $imgHeight > $sizes['h']) {
			$img->image_resize     = true;
			$img->image_ratio_y    = true;
			$img->image_ratio_crop = true;
			$img->image_x          = $sizes['w'];
			$cropSize              = ((($img->image_dst_y * $sizes['w']) / $img->image_dst_x) - $sizes['h']) / 2;
			$img->image_crop       = array($cropSize, 0, $cropSize, 0);
		}
		return $img;
	}

}