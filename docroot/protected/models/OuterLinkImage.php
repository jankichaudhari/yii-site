<?php
class OuterLinkImage extends File
{
	const RECORD_TYPE = 'OuterLinkImage';
	public $recordType = self::RECORD_TYPE;

	public function defaultScope()
	{

		return array(
			'order'     => 'displayOrder ASC',
			'condition' => "recordType = '" . self::RECORD_TYPE . "'",
		);
	}

	public function beforeSave()
	{

		try {
			if ($this->recordId) {
				$this->deleteAllByAttributes(array(
												  'recordId'   => $this->recordId,
												  'recordType' => self::RECORD_TYPE,
											 ));
				$this->wipeFolder();
			}

			$this->checkFolder();
			$this->name     = $this->realName = $this->file->getName();
			$this->mimeType = $this->file->getType();
			$this->fullPath = $this->getFolderPath() . '/' . $this->name;
			$this->file->saveAs($this->fullPath);
		} catch (Exception $e) {
			$this->addError('id', $e->getMessage());
			return false;
		}

		return parent::beforeSave();
	}

	public function rules()
	{

		return CMap::mergeArray(array(
									 array('file', 'required', 'on' => 'insert'), // we cant pull this up as all Place images will not follow this contract
									 array('file', 'file', 'types' => 'jpg,gif,png,jpeg', 'allowEmpty' => true),
								), parent::rules());
	}

	public function checkFolder()
	{

		$path = $this->getFolderPath();
		if (!file_exists($path) && !FileSystem::createDirectory($path)) {
			throw new RuntimeException('Folder to store images for ' . get_class($this) . ' doesnt exist');
		}
		return true;
	}

	/**
	 * @return string
	 */
	public function getFolderPath()
	{

		return Yii::app()->params['imgPath'] . '/' . static::RECORD_TYPE . '/' . $this->recordId;
	}

	public function getUrlToFile()
	{

		return Yii::app()->params['imgUrl'] . '/' . static::RECORD_TYPE . '/' . $this->recordId . '/' . $this->name;
	}

	private function wipeFolder()
	{

		return FileSystem::removeDirectory($this->getFolderPath());
	}
}