<?php

/**
 * This is the model class for table "devnotes".
 *
 * The followings are the available columns in table 'devnotes':
 * @property integer $id
 * @property string  $pageId
 * @property string  $text
 * @property double  $posX
 * @property double  $posY
 * @property integer $width
 * @property integer $height
 * @property integer $userId
 * @property integer $parentNoteId
 * @property string  $forUsers
 */
class Devnote extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Devnote the static model class
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

		return 'devnotes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('width, height, userId, parentNoteId', 'numerical', 'integerOnly'=> true),
			array('posX, posY', 'numerical'),
			array('pageId', 'length', 'max'=> 64),
			array('forUsers', 'length', 'max'=> 255),
			array('text', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, pageId, text, posX, posY, width, height, userId, parentNoteId, forUsers', 'safe', 'on'=> 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => [self::BELONGS_TO, 'User', 'userId'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'           => 'ID',
			'pageId'       => 'Page',
			'text'         => 'Text',
			'posX'         => 'Pos X',
			'posY'         => 'Pos Y',
			'width'        => 'Width',
			'height'       => 'Height',
			'userId'       => 'User',
			'parentNoteId' => 'Parent Note',
			'forUsers'     => 'For Users',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('pageId', $this->pageId, true);
		$criteria->compare('text', $this->text, true);
		$criteria->compare('posX', $this->posX);
		$criteria->compare('posY', $this->posY);
		$criteria->compare('width', $this->width);
		$criteria->compare('height', $this->height);
		$criteria->compare('userId', $this->userId);
		$criteria->compare('parentNoteId', $this->parentNoteId);
		$criteria->compare('forUsers', $this->forUsers, true);

		return new CActiveDataProvider($this, array(
												   'criteria'=> $criteria,
											  ));
	}

	public function findNotesForCurrentAction()
	{

		$criteria = new CDbCriteria();
		$criteria->compare('pageId', self::getPageId());
		return $this->findAll($criteria);
	}

	public static function getPageId()
	{
		$controller = Yii::app()->getController();
		$action     = Yii::app()->getController()->getAction();
		return  md5($controller->id . '/' . $action->id);
	}

	protected function beforeSave()
	{
		if($this->isNewRecord) {
			$this->userId = Yii::app()->user->id;
		}
		return parent::beforeSave();
	}

}