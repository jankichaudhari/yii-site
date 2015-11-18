<?php
/**
 * This is the model class for table "media".
 *
 * The followings are the available columns in table 'media':
 * @property integer $med_id
 * @property string  $med_table
 * @property integer $med_row
 * @property string  $med_type
 * @property integer $med_order
 * @property string  $med_title
 * @property string  $med_file
 * @property string  $med_realname
 * @property string  $med_filetype
 * @property integer $med_filesize
 * @property string  $med_blurb
 * @property string  $med_dims
 * @property string  $med_features
 * @property string  $med_created
 *
 * @property Deal    $instruction
 */
class Media extends CActiveRecord
{
	const TYPE_PHOTO                    = 'Photograph';
	const TYPE_FLOORPLAN                = 'Floorplan';
	const TYPE_EPC                      = 'EPC';
	const MAX_AVAILABLE_GIF_PIXEL_COUNT = 1152000;
	const SUFFIX_EMPTY                  = '';
	const SUFFIX_FULL                   = '_full';
	const SUFFIX_ORIGINAL               = '_original';
	const SUFFIX_LARGE                  = '_large';
	const SUFFIX_SMALL                  = '_small';
	const SUFFIX_THUMB1                 = '_thumb1';
	const SUFFIX_THUMB2                 = '_thumb2';
	const MAX_AVAILABLE_SIZE            = 819200;
	const MAX_RESIZE_WIDTH              = 1280;
	const MAX_RESIZE_HEIGHT             = 1280;

	/**
	 * @var CUploadedFile
	 */
	public $file;
	/**
	 * @var
	 */
	public $otherMedia;

	/**
	 * @var array
	 */
	public $cropFactor = array();
	/**
	 * @var int
	 */
	public $resizeWidth = self::MAX_RESIZE_WIDTH;
	/**
	 * @var int
	 */
	public $resizeHeight = self::MAX_RESIZE_HEIGHT;
	/**
	 * @var array sizes of EPC & Floorplans
	 */
	public $otherMediaSizes = array(
		'_original' => ['w' => self::MAX_RESIZE_WIDTH, 'h' => self::MAX_RESIZE_HEIGHT],
		'_full'     => ['w' => 652, 'h' => 652],
		'_thumb1'   => ['w' => 146, 'h' => 146],
		'_thumb2'   => ['w' => 56, 'h' => 56],
	);
	/**
	 * @var array sizes of Photos
	 */
	public $photoCropSizes = array(
		'_original' => ['w' => self::MAX_RESIZE_WIDTH, 'h' => self::MAX_RESIZE_HEIGHT],
		'_full'     => ['w' => 652, 'h' => 652],
		'_large'    => ['w' => 400, 'h' => 400],
		'_small'    => ['w' => 200, 'h' => 200],
		'_thumb1'   => ['w' => 146, 'h' => 146],
		'_thumb2'   => ['w' => 56, 'h' => 56],
	);
	/**
	 * @var array
	 */
	private $imageSizes = Array(
		''         => '',
		'original' => self::SUFFIX_ORIGINAL,
		'full'     => self::SUFFIX_FULL,
		'large'    => self::SUFFIX_LARGE,
		'small'    => self::SUFFIX_SMALL,
		'thumb1'   => self::SUFFIX_THUMB1,
		'thumb2'   => self::SUFFIX_THUMB2,
	);

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Media the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	public static function copyRecords($from, $to)
	{

		$sql = "INSERT INTO media (med_table,med_row,med_type,med_order,med_title,med_file,med_realname,med_filetype,med_filesize,med_blurb,med_dims,med_features,med_created,width,height,orientation)
						SELECT med_table,:to,med_type,med_order,med_title,med_file,med_realname,med_filetype,med_filesize,med_blurb,med_dims,med_features,:created,width,height,orientation FROM media WHERE med_row=:from";
		if ($rows = Yii::app()->db->createCommand($sql)->execute(array(
																	  ':to'      => $to,
																	  ':from'    => $from,
																	  ':created' => date('Y-m-d H:i:s'),
																 ))
		) {
			Locale::copyDirectory(Yii::app()->params['imgPath'] . '/property/p/' . $from, Yii::app()->params['imgPath'] . '/property/p/' . $to);
		}
		return $rows;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'media';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('file', 'file', 'types' => 'jpg, gif, png', 'on' => 'insert'),
			array('med_row', 'required'),
			array('med_table', 'default', 'value' => 'deal'),
			array('med_title', 'in', 'range' => array_merge(self::getPhotoTitles(), self::getFloorplanTitles())),
			array(
				'med_id, med_table, med_row, med_type, med_order, med_title, med_file, med_realname, med_filetype, med_filesize, med_blurb, med_dims, med_features, med_created',
				'safe', 'on' => 'copy'
			),
			array(
				'med_id, med_table, med_row, med_type, med_order, med_title, med_file, med_realname, med_filetype, med_filesize, med_blurb, med_dims, med_features, med_created',
				'safe', 'on' => 'search'
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'instruction' => array(self::BELONGS_TO, 'Deal', 'med_row'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'med_id'       => 'ID',
			'med_table'    => 'Table',
			'med_row'      => 'Row',
			'med_type'     => 'Type',
			'med_order'    => 'Order',
			'med_title'    => 'Title',
			'med_file'     => 'File',
			'med_realname' => 'Realname',
			'med_filetype' => 'Filetype',
			'med_filesize' => 'Filesize',
			'med_blurb'    => 'Blurb',
			'med_dims'     => 'Dims',
			'med_features' => 'Features',
			'med_created'  => 'Created',
			'width'        => 'Width',
			'height'       => 'Height',
			'orientation'  => 'Orientation',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria = new CDbCriteria;
		return new CActiveDataProvider($this, array('criteria' => $criteria));
	}

	public function getImageSizes()
	{

		if ($this->med_type == self::TYPE_FLOORPLAN || $this->med_type == self::TYPE_EPC) {
			$notAvlSizes      = ['_small', '_large'];
			$this->imageSizes = array_diff($this->imageSizes, $notAvlSizes);
		}
		return $this->imageSizes;
	}

	public function getMediaImageURIPath($type = '')
	{

		$fullName = $this->getNameWithSuffix($type);
		return '/images/property/p/' . $this->med_row . '/' . $fullName;
	}

	public function getOrgImageURIPath()
	{

		if ($this->getFullPath(self::SUFFIX_ORIGINAL)) {
			return $this->getMediaImageURIPath(self::SUFFIX_ORIGINAL);
		} else {
			return false;
		}
	}

	public function getImageURIPath()
	{

		return '/images/property/p/' . $this->med_row . '/' . $this->med_file;
	}

	/**
	 * @return string returns absolute URI path which is effectively URL to be used in the web.
	 * NOT to be parsed as local path on the server
	 */
	public function getThumbImageURIPath()
	{

		return '/images/property/p/' . $this->med_row . '/' . basename($this->getThumbMediaImgPath());
	}

	/**
	 *
	 * returns local path on server to the image thumbnail.
	 * because there may be different sizes of thumbnails this method returns them in order
	 * 1. _s - old suffix but still exists for some EPC
	 * 2. SUFFIX_THUMB1 - first thumbnail
	 * 3. SUFFIX_THUMB2 - second thumbnail (which is smaller i presume)
	 *
	 * try to avoid usage of this function, return of this function is not predictable in some cases.
	 *
	 * @return bool|string returns local path on server to the image thumbnail.
	 *
	 * @deprecated since revision:1220
	 *
	 */
	public function getThumbMediaImgPath()
	{

		$folderPath = realpath(Yii::app()->params['imgPath'] . '/property/p/' . $this->med_row);

		if (file_exists($folderPath . '/' . $this->getNameWithSuffix(self::SUFFIX_THUMB1))) {
			return $folderPath . '/' . $this->getNameWithSuffix(self::SUFFIX_THUMB1);
		}
		if (file_exists($folderPath . '/' . $this->getNameWithSuffix(self::SUFFIX_THUMB2))) {
			return $folderPath . '/' . $this->getNameWithSuffix(self::SUFFIX_THUMB2);
		}
		return false;
	}

	public function getPhotoTitles()
	{

		return array(
			''              => '',
			'Exterior'      => 'Exterior',
			'Reception 1'   => 'Reception 1',
			'Reception 2'   => 'Reception 2',
			'Reception 3'   => 'Reception 3',
			'Reception 4'   => 'Reception 4',
			'Dining Room'   => 'Dining Room',
			'Dining Area'   => 'Dining Area',
			'Kitchen'       => 'Kitchen',
			'Hall'          => 'Hall',
			'Stairs'        => 'Stairs',
			'Landing'       => 'Landing',
			'Bedroom 1'     => 'Bedroom 1',
			'Bedroom 2'     => 'Bedroom 2',
			'Bedroom 3'     => 'Bedroom 3',
			'Bedroom 4'     => 'Bedroom 4',
			'Bedroom 5'     => 'Bedroom 5',
			'Bedroom 6'     => 'Bedroom 6',
			'Bedroom 7'     => 'Bedroom 7',
			'Bedroom 8'     => 'Bedroom 8',
			'Bathroom 1'    => 'Bathroom 1',
			'Bathroom 2'    => 'Bathroom 2',
			'Bathroom 3'    => 'Bathroom 3',
			'Bathroom 4'    => 'Bathroom 4',
			'Wet Room'      => 'Wet Room',
			'Shower Room'   => 'Shower Room',
			'En suite'      => 'En suite',
			'W.C.'          => 'W.C.',
			'Mezzanine'     => 'Mezzanine',
			'Utility Room'  => 'Utility Room',
			'Dressing Room' => 'Dressing Room',
			'Play Room'     => 'Play Room',
			'Study'         => 'Study',
			'Conservatory'  => 'Conservatory',
			'Garden'        => 'Garden',
			'Grounds'       => 'Grounds',
			'Rear'          => 'Rear',
			'Balcony'       => 'Balcony',
			'Roof Terrace'  => 'Roof Terrace',
			'View'          => 'View',
			'Basement'      => 'Basement',
			'Cellar'        => 'Cellar',
			'Store'         => 'Store',
			'Loft'          => 'Loft',
			'Attic'         => 'Attic',
			'Gym'           => 'Gym',
			'Studio'        => 'Studio',
			'Shop'          => 'Shop',
			'Garage'        => 'Garage',
			'Room'          => 'Room',
			'Feature'       => 'Feature'
		);
	}

	public static function getFloorplanTitles()
	{

		return array(
			'Lower Ground Floor' => 'Lower Ground Floor',
			'Ground Floor'       => 'Ground Floor',
			'Upper Ground Floor' => 'Upper Ground Floor',
			'First Floor'        => 'First Floor',
			'Second Floor'       => 'Second Floor',
			'Third Floor'        => 'Third Floor',
			'Fourth Floor'       => 'Fourth Floor',
			'Fifth Floor'        => 'Fifth Floor',
			'Sixth Floor'        => 'Sixth Floor',
			'Seventh Floor'      => 'Seventh Floor',
			'Eight Floor'        => 'Eighth Floor',
			'Ninth Floor'        => 'Ninth Floor',
			'Tenth Floor'        => 'Tenth Floor',
			'Eleventh Floor'     => 'Eleventh Floor',
			'Twelfth Floor'      => 'Twelfth Floor',
			'Thirteenth Floor'   => 'Thirteenth Floor',
			'Fourteenth Floor'   => 'Fourteenth Floor',
			'Fifteenth Floor'    => 'Fifteenth Floor',
			'Sixteenth Floor'    => 'Sixteenth Floor',
			'Seventeenth Floor'  => 'Seventeenth Floor',
			'Eighteenth Floor'   => 'Eighteenth Floor',
			'Nineteenth Floor'   => 'Nineteenth Floor',
			'Twentieth Floor'    => 'Twentieth Floor',
			'Mezzanine'          => 'Mezzanine',
			'Attic'              => 'Attic',
			'Garage'             => 'Garage',
			'Out Building'       => 'Out Building',
			'Cellar/Basement'    => 'Cellar/Basement',
			'Garden'             => 'Garden',
			'Land'               => 'Land',
			'Entire Plot'        => 'Entire Plot',
			'Roof Terrace'       => 'Roof Terrace',
		);
	}

	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->med_created = date('Y-m-d H:i:s');

			if (!$this->med_row) {
				throw new CDbException('Cant save the media if it does not belong to any record');
			}
			$sql             = "SELECT IF(MAX(med_order) IS NOT NULL, MAX(med_order), 0) as max_ord  FROM " . $this->tableName() . " WHERE med_row = '" . $this->med_row . "' AND med_type = '" . $this->med_type . "'";
			$command         = Yii::app()->db->createCommand($sql);
			$result          = $command->queryRow();
			$this->med_order = $result['max_ord'] + 1;
		}

		if ($this->file) {
			$this->med_type = (isset($this->otherMedia) && $this->otherMedia) ? $this->otherMedia : self::TYPE_PHOTO;

			$fileTempName = $this->file->tempName;
			list($imgWidth, $imgHeight, $type, $attr) = getimagesize($fileTempName);

			if (strtolower($this->file->extensionName) == 'gif' && $imgHeight * $imgWidth > self::MAX_AVAILABLE_GIF_PIXEL_COUNT) {
				$this->addError('file', 'Sorry, but GIF image is too large!');
				return false;
			}

			$fileName           = $this->generateUniqFileName();
			$this->med_realname = $this->file->getName();
			$this->med_filetype = $this->file->getType();
			$this->med_filesize = $this->file->getSize();
			$ext                = $this->file->getExtensionName();
			$extension          = '.' . $ext;
			$filePath           = $this->getLocalPath();
			$this->med_file     = $fileName . $extension;

			/** @var $imageTool \upload */
			$imageTool                     = Yii::app()->imagemod->load($fileTempName);
			$imageTool->file_new_name_body = $fileName;
			$imageTool->file_new_name_ext  = $ext;
			$imageTool->process($filePath);

			$imageDimType = null;
			if ($imgHeight > $imgWidth) { //vertical image
				$imageDimType = 'vertical';
				if ($imgHeight > $this->resizeHeight) {
					$imageTool = $this->resizeImageTool($imageTool, $imageDimType, $this->resizeHeight);
				}
			} else { //horizontal image
				$imageDimType = 'horizontal';
				if ($imgWidth > $this->resizeWidth) {
					$imageTool = $this->resizeImageTool($imageTool, $imageDimType, $this->resizeWidth);
				}
			}
			$imageTool->file_new_name_body = $fileName . self::SUFFIX_ORIGINAL;
			$imageTool->file_new_name_ext  = $ext;
			$imageTool->process($filePath);

			/*floorplan OR epc*/
			if (isset($this->otherMedia) && $this->otherMedia) {
				foreach ($this->otherMediaSizes as $suffix => $sizes) {
					if (($imageDimType == 'vertical') && ($imgHeight > $sizes['h'])) {
						$imageTool->image_resize  = true;
						$imageTool->image_ratio_x = true;
						$imageTool->image_y       = $sizes['h'];
					} else {
						if (($imageDimType == 'horizontal') && ($imgWidth > $sizes['w'])) {
							$imageTool->image_resize  = true;
							$imageTool->image_ratio_y = true;
							$imageTool->image_x       = $sizes['w'];
						}
					}
					$imageTool->file_new_name_body = $fileName . $suffix;
					$imageTool->file_new_name_ext  = $ext;
					$imageTool->process($filePath);
				}
			} else { // photograph
				$croppedFilePath = '';
				if (isset($this->cropFactor['w'])) {
					$imageTool->image_ratio_crop   = true;
					$cropWidth                     = ($this->cropFactor['w'] * $imgWidth) / $this->cropFactor['width'];
					$top                           = ($this->cropFactor['y'] * $imgHeight) / $this->cropFactor['height'];
					$left                          = ($this->cropFactor['x'] * $imgWidth) / $this->cropFactor['width'];
					$right                         = $imgWidth - ($left + $cropWidth);
					$bottom                        = $imgHeight - ($top + $cropWidth);
					$imageTool->image_crop         = array($top, $right, $bottom, $left);
					$imageTool->file_new_name_body = $fileName . '_crop';
					$imageTool->file_new_name_ext  = $ext;
					$imageTool->process($filePath);

					// Load cropped file and resize
					$croppedFilePath = $filePath . '/' . $fileName . '_crop' . $extension;
					$imageTool       = Yii::app()->imagemod->load($croppedFilePath);
				}

				foreach ($this->photoCropSizes as $suffix => $sizes) {
					if ($suffix != '_original') {
						if ($imageDimType == 'vertical') { //horizontal image
							if (!isset($this->cropFactor['h'])) {
								$imageTool->image_ratio_crop = true;
								$top                         = $bottom = 0;
								$left                        = $right = (((($imgWidth * $sizes['h']) / $imgHeight) - $sizes['w']) / 2) + 1;
								$imageTool->image_crop       = array($top, $right, $bottom, $left);
							}
							$imageTool->image_resize  = true;
							$imageTool->image_y       = $sizes['h'];
							$imageTool->image_ratio_x = true;

						} else if ($imageDimType == 'horizontal') {
							if (!isset($this->cropFactor['w'])) {
								$imageTool->image_ratio_crop = true;
								$top                         = ((($imgHeight * $sizes['w']) / $imgWidth) - $sizes['h']) / 2;
								$bottom                      = $top + 1;
								$left                        = $right = 0;
								$imageTool->image_crop       = array($top, $right, $bottom, $left);
							}
							$imageTool->image_resize  = true;
							$imageTool->image_x       = $sizes['w'];
							$imageTool->image_ratio_y = true;
						}

						$imageTool->file_new_name_body = $fileName . $suffix;
						$imageTool->file_new_name_ext  = $ext;
						$imageTool->process($filePath);
					}
				}
				if (file_exists($croppedFilePath)) {
					unlink($croppedFilePath);
				}
			}
		}
		return parent::beforeSave();
	}

	private function resizeImageTool($imageTool, $type = 'horizontal', $size = 0)
	{

		if (!$imageTool || !$size) {
			throw new RuntimeException('resize image tool or size not defined');
		}
		$imageTool->image_resize = true;
		if ($type == 'vertical') {
			$imageTool->image_ratio_x = true;
			$imageTool->image_y       = $size;
		} else {
			$imageTool->image_ratio_y = true;
			$imageTool->image_x       = $size;
		}

		return $imageTool;
	}

	protected function beforeDelete()
	{

		$filePath    = $this->getLocalPath();
		$orgFileName = realpath($filePath . '/' . $this->med_file);
		if (file_exists($orgFileName)) {
			unlink($orgFileName);
		}
		switch ($this->med_type) {
			case self::TYPE_EPC :
			case self::TYPE_FLOORPLAN :
				foreach ($this->otherMediaSizes as $suffix => $sizes) {
					$fullName = $this->getNameWithSuffix($suffix);
					$fileName = realpath($filePath . '/' . $fullName);
					if (file_exists($fileName)) {
						unlink($fileName);
					}
				}
				break;
			case self::TYPE_PHOTO :
				foreach ($this->photoCropSizes as $suffix => $sizes) {
					$fullName = $this->getNameWithSuffix($suffix);
					$fileName = realpath($filePath . '/' . $fullName);
					if (file_exists($fileName)) {
						unlink($fileName);
					}
				}
				break;
		}
		return parent::beforeDelete();
	}

	private function generateUniqFileName()
	{

		if (!$this->instruction->property->address) {
			throw new Exception('can not generate name of the media file for a property without address');
		}
		$type = ($this->med_type == self::TYPE_PHOTO) ? '' : $this->med_type . "_";
		$str  = str_replace([
							" ", "'", "-", ".", ","
							], '_', $type . $this->instruction->property->address->line1 . '_' . $this->instruction->property->address->getPostcodePart());
		return uniqid($str . '_');
	}

	/**
	 * @param array $cropData
	 *
	 */
	public function setCropFactor(Array $cropData)
	{

		$this->cropFactor = $cropData;
	}

	private function getLocalPath()
	{

		return Yii::app()->params['imgPath'] . '/property/p/' . $this->med_row;
	}

	public function rearrange($newOrder, $instructionId, $type = 'Photograph')
	{

		$cases = '';
		foreach ($newOrder as $orderNum => $id) {
			$cases .= " WHEN med_id = " . $id . " THEN " . ($orderNum + 1) . "";
		}

		$sql = "UPDATE " . $this->tableName() . "
			SET med_order = CASE " . $cases . " END
			WHERE med_row = '" . $instructionId . "' AND med_type = '" . $type . "'";
		Yii::app()->db->createCommand($sql)->execute();
	}

	public function getFullPath($size)
	{

		$fullName = $this->getNameWithSuffix($size);
		$fullPath = Yii::app()->params['imgPath'] . '/property/p/' . $this->med_row . '/' . $fullName;
		return file_exists($fullPath) ? $fullPath : false;
	}

	protected function getNameWithSuffix($suffix)
	{

		$imageSizes = $this->getImageSizes();
		if (!in_array($suffix, $imageSizes)) {
			if (array_key_exists($suffix, $imageSizes)) {
				$suffix = $imageSizes[$suffix];
			} else {
				$suffix = reset($imageSizes);
			}
		}
		return preg_replace('/\.(jpg|jpeg|gif|png)/i', $suffix . '.$1', $this->med_file);
	}
}
