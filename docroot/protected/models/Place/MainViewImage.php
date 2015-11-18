<?php
namespace application\models\Place;
use \Yii as Yii;

/**
 * These images are displayed on a listing pages.
 * before you go into the details of a Place
 *
 * it has 3 sizes:
 * small, thumb and another one, which is NOT main as it may look.
 * it doesn't have any suffix and is saved as it is
 *
 * Class MainViewImage
 * @package application\models\Place
 */
final class MainViewImage extends Image
{
	const RECORD_TYPE = 'PlaceMainView';

	public $smallName = '';
	public $mediumName = '';

	protected $smallSuffix = '_small';
	protected $mediumSuffix = '_medium';

	protected $resizeSizes = array(
		'_small'  => array(
			'w' => 146,
			'h' => 98
		),
		'_medium' => array(
			'w' => 310,
			'h' => 206
		),
		'_main'   => array(
			'w' => 1280,
			'h' => 1024
		)
	);

	protected $sizes = array('small', 'medium');

	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	protected function beforeSave()
	{

		list($basename, $ext) = explode(".", $this->name);
		if (file_exists($this->fullPath . "/" . $this->name)) {
			$template     = $this->fullPath . "/" . $basename;
			$watermarkImg = Yii::app()->params['imgPath'] . '/watermark.png';

			foreach ($this->resizeSizes as $key => $sizes) {
				if (!file_exists($template . $key . "." . $ext)) {

					/** @var $img ImageTool */
					$fullImageName = ($this->fullPath) . "/" . $this->name;
					list($thisWidth, $thisHeight, $type, $attr) = getimagesize($fullImageName);
					$img     = Yii::app()->imagemod->load($fullImageName);
					$imgPath = Yii::app()->params['imgPath'] . '/watermark.png';
					switch ($key) {
						case '_small' :
							if (($thisWidth > $sizes['w']) || ($thisHeight > $sizes['h'])) {
								$img->image_resize     = true;
								$img->image_x          = $sizes['w'];
								$img->image_ratio_y    = true;
								$img->image_ratio_crop = true;
								$cropSize              = ((($img->image_dst_y * $sizes['w']) / $img->image_dst_x) - $sizes['h']) / 2;
								$img->image_crop       = array($cropSize, 0, $cropSize, 0);
							}
							$img->file_new_name_body = $basename . $key;
							break;
						case '_medium' :
							if (($thisWidth > $sizes['w']) || ($thisHeight > $sizes['h'])) {
								$img->image_resize     = true;
								$img->image_x          = $sizes['w'];
								$img->image_ratio_y    = true;
								$img->image_ratio_crop = true;
								$cropSize              = ((($img->image_dst_y * $sizes['w']) / $img->image_dst_x) - $sizes['h']) / 2;
								$img->image_crop       = array($cropSize, 0, $cropSize, 0);
							}
							$img->file_new_name_body = $basename . $key;
							break;
						default :
							$img->image_watermark          = $watermarkImg;
							$img->image_watermark_x        = 5;
							$img->image_watermark_y        = -5;
							$img->image_watermark_position = 'LB';
							$img->file_new_name_body       = $basename;
					}
					$img->file_overwrite    = true;
					$img->file_new_name_ext = $ext;
					$img->process($this->fullPath);
				}
			}
		} else {
			$this->addError('file', 'File ' . $this->name . ' does not exist for  record id:' . $this->id);
		}

		return parent::beforeSave() && !$this->hasErrors();
	}

	protected function beforeDelete()
	{

		$filePath = $this->fullPath;
		$orgFile  = $filePath . '/' . $this->name;
		if (file_exists($orgFile)) {
			unlink($orgFile);
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
}