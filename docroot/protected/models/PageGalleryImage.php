<?php
/**
 * @method \PageGalleryImage officePhotos() scope to return office images
 */
class PageGalleryImage extends Image
{
	const SUFFIX_THUMB = '_thumb';
	const SUFFIX_SMALL = '_small';
	const SUFFIX_LARGE = '_large';
	const SUFFIX_FULL  = '_full';

	public $thumbName = '';
	public $smallName = '';
	public $largeName = '';
	public $fullName = '';

	protected $thumbSuffix = self::SUFFIX_THUMB;
	protected $smallSuffix = self::SUFFIX_SMALL;
	protected $largeSuffix = self::SUFFIX_LARGE;
	protected $fullSuffix = self::SUFFIX_FULL;

	protected $resizeWidth = 1600;
	protected $resizeHeight = 1280;

	public $imageSizes = array(
		''      => '',
		'thumb' => self::SUFFIX_THUMB,
		'small' => self::SUFFIX_SMALL,
		'large' => self::SUFFIX_LARGE,
		'full'  => self::SUFFIX_FULL,
	);

	protected $resizeSizes = array(
		'_thumb' => array(
			'w' => 75,
			'h' => 75
		),
		'_small' => array(
			'w' => 146,
			'h' => 146
		),
		'_large' => array(
			'w' => 400,
			'h' => 400
		),
		'_full'  => array(
			'w' => 652,
			'h' => 652
		)
	);

	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	public function scopes()
	{

		return array(
			'officePhotos' => array(
				'condition' => "recordType = 'ContactPageGallery'"
			),
		);
	}

	protected function beforeSave()
	{

		try {
			$tempName = explode(".", $this->file->name);
//            $fileName = (str_replace([" ", "'", "-", ",", "."],"_",reset($tempName))) . Util::getRandomString(5);
			$fileName  = preg_replace("/[^a-zA-Z0-9]/", "_", reset($tempName)) . Util::getRandomString(5);
			$ext       = end($tempName);
			$imageName = $fileName . "." . $ext;

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

			foreach ($this->resizeSizes as $key => $sizes) {
				if (($width > $sizes['w']) || ($height > $sizes['h'])) {
					if (($width > $height)) { //horizontal image
						$top       = $bottom = 0;
						$left      = $right = (ceil(((($width * $sizes['h']) / $height) - $sizes['w']) / 2) + 1);
						$imageTool = $this->cropImageTool($imageTool, $top, $right, $bottom, $left);
						$imageTool = $this->resizeImageTool($imageTool, $x = 0, $y = $sizes['h'], $ratio_x = true, $ratio_y = false);
					} else {
						$top       = ceil((($height * $sizes['w']) / $width) - $sizes['h']) / 2;
						$bottom    = $top + 1;
						$left      = $right = 0;
						$imageTool = $this->cropImageTool($imageTool, $top, $right, $bottom, $left);
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

			return parent::beforeSave();
		} catch (Exception $e) {
			$this->addError('id', $e->getMessage());
			return false;
		}
	}

}

?>