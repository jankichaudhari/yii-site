<?php

/**
 * This is the model class for table "source".
 *
 * The followings are the available columns in table 'source':
 * @property integer $sou_id
 * @property string  $sou_title
 * @property integer $sou_type
 * @property string  $sou_status
 */
class Source extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Source the static model class
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

		return 'source';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sou_type', 'numerical', 'integerOnly' => true),
			array('sou_title', 'length', 'max' => 100),
			array('sou_status', 'length', 'max' => 8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('sou_id, sou_title, sou_type, sou_status', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'sou_id'     => 'Id',
			'sou_title'  => 'Title',
			'sou_type'   => 'Type',
			'sou_status' => 'Sou Status',
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

		$criteria->compare('sou_id', $this->sou_id);
		$criteria->compare('sou_title', $this->sou_title, true);
		$criteria->compare('sou_type', $this->sou_type);
		$criteria->compare('sou_status', $this->sou_status, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}

	public function getTitle($withType = true)
	{

		$title = $this->sou_title ? $this->sou_title : "(undefined)";
		if ($withType && $this->sou_type) {
			$type = self::model()->findByPk($this->sou_type);
			if ($type) {
				$title = $title . " (" . $type->sou_title . ")";
			}
		}

		return $title;
	}

	public function getTypes($parent = 0)
	{

		$criteria = new CDbCriteria();
		$criteria->compare("sou_type", $parent);
		return $this->findAll($criteria);
	}

}