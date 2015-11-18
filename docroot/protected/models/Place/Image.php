<?php
namespace application\models\Place;
use RuntimeException;
use \Yii as Yii;
use \File as File;

abstract class Image extends File
{
	/**
	 * @var array array of possible sizes; these must match names of variables
	 */
	protected $sizes = null;
	protected $resizeSizes = null;

	/** @var \CUploadedFile */
	public $picture;

	public function getSizes()
	{

		return $this->sizes;
	}

	public function populateNames()
	{

		$this->fullPath = rtrim($this->fullPath, " /");
		list($basename, $ext) = explode(".", $this->name);
		$basename = pathinfo($this->name, PATHINFO_FILENAME);
		$ext      = pathinfo($this->name, PATHINFO_EXTENSION);
		$template = $this->fullPath . "/" . $basename;

		foreach ($this->getSizes() as $key => $size) {
			$nameVar   = $size . 'Name';
			$suffixVar = $size . 'Suffix';

			if (!isset($this->$nameVar)) {
				throw new RuntimeException('Cannot set name variable for class ' . get_class($this) . '[name : ' . $size . ']');
			}
			if (!isset($this->$suffixVar)) {
				throw new RuntimeException('Suffix var does not exist ' . get_class($this) . ' [suffix name : ' . $size . ']');
			}

			$this->$nameVar = $basename . $this->$suffixVar . '.' . $ext;
		}

	}

	protected function afterFind()
	{

		$this->populateNames();
		parent::afterFind();
	}

	public function getResizeSizes()
	{

		return $this->resizeSizes;
	}

	/**
	 * @param $resizeSizes
	 * @deprecated since r1238 as not making sense to set it externaly
	 */
	public function setResizeSizes($resizeSizes)
	{

		$this->resizeSizes = $resizeSizes;
	}
}
