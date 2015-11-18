<?php
class Image extends File
{
	protected $resizeSizes = null;
	protected $resizeWidth = 1280;
	protected $resizeHeight = 1024;

	public $folderName = '';
	public $imageSizes = array(
		'' => ''
	);

	/**
	 * @return array
	 */
	public function defaultScope()
	{

		return array(
			'order' => 'displayOrder ASC'
		);
	}

	public function rules()
	{

		return CMap::mergeArray(array(
									 array('file', 'file', 'types' => 'jpg,gif,png,jpeg', 'allowEmpty' => true),
								), parent::rules());
	}

	protected function afterFind()
	{

		if ($this->name && $this->fullPath):
			$this->fullPath = rtrim($this->fullPath, " /");
			list($baseName, $ext) = explode(".", $this->name);
			$template = $this->fullPath . "/" . $baseName;
			foreach ($this->imageSizes as $key => $value) {
				if (!empty($key)):
					$sizeVar = $key . "Name";
					$suffix  = $key . "Suffix";

					if (!isset($this->$sizeVar)) {
						throw new RuntimeException('Cannot set name variable for class ' . get_class($this) . '[name : ' . $key . ']');
					}
					if (!isset($this->$suffix)) {
						throw new RuntimeException('Suffix var does not exist ' . get_class($this) . ' [suffix name : ' . $key . ']');
					}

					$this->$sizeVar = basename($template . $this->$suffix . "." . $ext);
				endif;
			}
		endif;

		parent::afterFind();
	}

	public function getFolderPath()
	{

		$folderName = $this->folderName ? $this->folderName : $this->recordType;
		return Yii::app()->params['imgPath'] . '/' . $folderName . '/' . $this->recordId;
	}

	public function getImageURIPath($type = '')
	{

		$imageSize = reset($this->imageSizes);
		if (array_key_exists($type, $this->imageSizes)) {
			$imageSize = $this->imageSizes[$type];
		} else if (in_array($type, $this->imageSizes)) {
			$imageSize = $type;
		}

		list($baseName) = explode(".", $this->name);
		$imageName = str_replace($baseName, $baseName . $imageSize, $this->name);
		return $this->getFolderPath() . '/' . $imageName;
	}

	protected function resizeImageTool($imageTool, $x = 0, $y = 0, $ratio_x = false, $ratio_y = false)
	{

		if (!$imageTool) {
			throw new RuntimeException('resize image tool not defined');
		}
		$imageTool->image_resize = true;
		if ($ratio_x) {
			$imageTool->image_ratio_x = true;
		}
		if ($ratio_y) {
			$imageTool->image_ratio_y = true;
		}
		if ($x) {
			$imageTool->image_x = $x;
		}
		if ($y) {
			$imageTool->image_y = $y;
		}
		return $imageTool;
	}

	protected function cropImageTool($imageTool, $top = 0, $right = 0, $bottom = 0, $left = 0)
	{

		if (!$imageTool) {
			throw new RuntimeException('crop image tool not defined');
		}
		$imageTool->image_ratio_crop = true;
		$imageTool->image_crop       = array($top, $right, $bottom, $left);
		return $imageTool;
	}

	protected function beforeDelete()
	{

		foreach ($this->imageSizes as $size) {
			$thisPath = $this->getImageURIPath($size);
			if (file_exists($thisPath)) {
				unlink($thisPath);
			}
		}

		return parent::beforeDelete();
	}
}

?>