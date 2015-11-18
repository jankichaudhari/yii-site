<?php
class LocalEventImage extends Image
{
	const SUFFIX_SMALL  = "_small";
	const SUFFIX_MEDIUM = "_medium";
	const SUFFIX_LARGE  = "_large";

	public $smallName = '';
	public $largeName = '';
	public $mediumName = '';

	protected $smallSuffix = self::SUFFIX_SMALL;
	protected $mediumSuffix = self::SUFFIX_MEDIUM;
	protected $largeSuffix = self::SUFFIX_LARGE;

	public $folderName = 'LocalEvent';

	public $cropFactor = array();

	public $imageSizes = array(
		''       => '',
		'small'  => self::SUFFIX_SMALL,
		'medium' => self::SUFFIX_MEDIUM,
		'large'  => self::SUFFIX_LARGE,
	);

	protected $resizeWidth = 1280;
	protected $resizeHeight = 1024;

	protected $resizeSizes = array(
		'_small'  => array(
			'w' => 146,
			'h' => 146
		),
		'_medium' => array(
			'w' => 200,
			'h' => 200
		),
		'_large'  => array(
			'w' => 600,
			'h' => 600
		),
	);

	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	protected function beforeSave()
	{

		try {
			if ($this->recordType == 'LocalEventMain') {
				$images = LocalEventImage::model()->findAllByAttributes([
																		'recordId'   => $this->recordId,
																		'recordType' => $this->recordType
																		]);
				if ($images) {
					foreach ($images as $image) {
						LocalEventImage::model()->findByPk($image->id)->delete();
					}
				}
			}

			$tempImageName = explode(".", $this->file->name);
			$fileName      = preg_replace("/[^a-zA-Z0-9]/", "_", reset($tempImageName));
			$ext           = end($tempImageName);
			$imageName     = $fileName . '.' . $ext;

			list($width, $height, $type, $attr) = getimagesize($this->file->tempName);
			Yii::app()->file->set($this->getFolderPath())->createDir(0777);
			/** @var $imageTool \upload */
			$imageTool = Yii::app()->imagemod->load($this->file->tempName);

			if ($height > $width) { //verticle image
				if ($height > $this->resizeHeight) {
					$imageTool = $this->resizeImageTool($imageTool, $x = 0, $y = $this->resizeHeight, $ratio_x = true, $ratio_y = false);
				}
			} else { //horizontal image
				if ($width > $this->resizeWidth) {
					$imageTool = $this->resizeImageTool($imageTool, $x = $this->resizeWidth, $y = 0, $ratio_x = false, $ratio_y = true);
				}
			}
			$imageTool->file_new_name_body = $fileName;
			$imageTool->file_new_name_ext  = $ext;
			$imageTool->process($this->getFolderPath());

			$croppedFilePath = '';
			if (isset($this->cropFactor['cropWidth'])) {
				$cropWidth                     = ($this->cropFactor['cropWidth'] * $width) / $width;
				$top                           = ($this->cropFactor['y'] * $height) / $this->cropFactor['height'];
				$left                          = ($this->cropFactor['x'] * $width) / $this->cropFactor['width'];
				$right                         = $width - ($left + $cropWidth);
				$bottom                        = $height - ($top + $cropWidth);
				$imageTool                     = $this->cropImageTool($imageTool, $top, $right, $bottom, $left);
				$imageTool->file_new_name_body = $fileName . '_crop';
				$imageTool->file_new_name_ext  = $ext;
				$imageTool->process($this->getFolderPath());
				$croppedFilePath = $this->getFolderPath() . '/' . $fileName . '_crop.' . $ext;
				$imageTool       = Yii::app()->imagemod->load($croppedFilePath);
			}

			foreach ($this->resizeSizes as $key => $sizes) {
				if (($width > $sizes['w']) || ($height > $sizes['h'])) {
					if (($width > $height)) { //horizontal image
						if (!isset($this->cropFactor['cropWidth'])) {
							$top       = $bottom = 0;
							$left      = $right = (((($width * $sizes['h']) / $height) - $sizes['w']) / 2) + 1;
							$imageTool = $this->cropImageTool($imageTool, $top, $right, $bottom, $left);
						}
						$imageTool = $this->resizeImageTool($imageTool, $x = 0, $y = $sizes['h'], $ratio_x = true, $ratio_y = false);

					} else {
						if (!isset($this->cropFactor['cropWidth'])) {
							$top       = ((($height * $sizes['w']) / $width) - $sizes['h']) / 2;
							$bottom    = $top + 1;
							$left      = $right = 0;
							$imageTool = $this->cropImageTool($imageTool, $top, $right, $bottom, $left);
						}
						$imageTool = $this->resizeImageTool($imageTool, $x = $sizes['w'], $y = 0, $ratio_x = false, $ratio_y = true);
					}
				}

				$imageTool->file_new_name_body = $fileName . $key;
				$imageTool->file_new_name_ext  = $ext;
				$imageTool->file_overwrite     = true;
				$imageTool->process($this->getFolderPath());
			}

			if ($imageTool->processed) {
				$this->name     = $this->realName = $imageName;
				$this->mimeType = $this->file->getType();
				$this->fullPath = $this->getFolderPath() . '/' . $this->name;
			}

			if (file_exists($croppedFilePath)) {
				unlink($croppedFilePath);
			}

		} catch (Exception $e) {
			$this->addError('id', $e->getMessage());
			return false;
		}

		return parent::beforeSave();
	}
}

?>