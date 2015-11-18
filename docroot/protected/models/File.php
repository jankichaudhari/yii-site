<?php

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 * @property string $id
 * @property string $recordId
 * @property string $recordType
 * @property string $name
 * @property string $realName
 * @property string $mimeType
 * @property string $created
 * @property string $createdBy
 * @property string $info
 * @property string $caption
 * @property string $fullPath
 *
 */
class File extends CActiveRecord
{
	/** @var CUploadedFile */
	public $file;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return File the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'file';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('file', 'file', 'types' => null, 'allowEmpty' => true),
			array('recordId, createdBy', 'length', 'max' => 10),
			array('recordType, name, realName, mimeType', 'length', 'max' => 255),
			array('id, recordId, recordType, name, realName, mimeType, created, createdBy, info, caption', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'           => 'ID',
			'recordId'     => 'Record',
			'recordType'   => 'Record Type',
			'name'         => 'Name',
			'realName'     => 'Real Name',
			'mimeType'     => 'Mime Type',
			'created'      => 'Created',
			'createdBy'    => 'Created By',
			'caption'      => 'Image Caption',
			'info'         => 'Info',
			'fullPath'     => 'Full Path',
			'displayOrder' => 'Display Order',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria = new CDbCriteria;
		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}

	public function rearrange($updateOrderIds, $recordId, $recordType)
	{

		if ($recordId && $recordType) {
			$cases = '';
			foreach ($updateOrderIds as $orderNum => $id) {
				$cases .= " WHEN id = " . $id . " THEN " . ($orderNum + 1) . "";
			}

			$sql = "UPDATE " . $this->tableName() . "
				SET displayOrder = CASE " . $cases . " END
				WHERE recordType = '" . $recordType . "'
				AND recordId = " . $recordId;

			Yii::app()->db->createCommand($sql)->execute();
		}
	}

	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->created   = date("Y-m-d H:i:s");
			$this->createdBy = Yii::app()->user->getId();
		}

		return parent::beforeSave();
	}

	protected function beforeDelete()
	{

		$filePath = $this->fullPath . '/' . $this->name;
		if (file_exists($filePath)) {
			unlink($filePath);
		}

		return parent::beforeDelete();
	}
}
