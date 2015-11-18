<?php

class BlogImage extends File
{
	/** @var  CUploadedFile */
	public $image;

	public $recordType = 'BlogImage';

	public function rules()
	{
		return CMap::mergeArray(parent::rules(), array(
													  ['image', 'file', 'types' => 'jpg, gif, png'],
												 ));
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function beforeSave()
	{

		if ($this->isNewRecord && !$this->image) {
			return false;
		}

		if ($this->isNewRecord) {
			$this->realName = $this->generateUniqueName();
			$this->name     = $this->realName;
			$this->fullPath = $this->getFullPath();
			$this->image->saveAs($this->getFullPath());
			$this->mimeType = $this->image->getType();
		}
		return parent::beforeSave();
	}

	public function generateUniqueName()
	{

		$fileName = basename($this->image->getName(), $this->image->getExtensionName());
		return uniqid($fileName) . "." . $this->image->getExtensionName();
	}

	public function getSavePath()
	{
		$imagePath = Yii::app()->params['blog']['imagePath'];
		if (!file_exists($imagePath)) {
			FileSystem::createDirectory($imagePath);
		}
		return $imagePath;

	}

	private function getFullPath()
	{
		return $this->fullPath ? : $this->getSavePath() . '/' . $this->realName;
	}

	/**
	 * too lazy to do smthing fancy here
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return '/images/blog/' . $this->name;
	}

}
