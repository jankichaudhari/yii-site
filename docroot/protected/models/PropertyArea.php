<?php

/**
 * This is the model class for table "area".
 *
 * The followings are the available columns in table 'area':
 * @property integer $are_id
 * @property string  $are_title
 * @property string  $are_postcode
 * @property integer $are_osx
 * @property integer $are_osy
 * @property string  $are_status
 * @property integer $are_branch
 */
class PropertyArea extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PropertyArea the static model class
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

		return 'area';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('are_osx, are_osy, are_branch', 'numerical', 'integerOnly' => true),
			array('are_title', 'length', 'max' => 50),
			array('are_postcode', 'length', 'max' => 5),
			array('are_status', 'length', 'max' => 8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('are_id, are_title, are_postcode, are_osx, are_osy, are_status, are_branch', 'safe', 'on' => 'search'),
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
			'areas' => [self::HAS_MANY, 'PropertyArea', 'are_postcode'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'are_id'       => 'Are',
			'are_title'    => 'Are Title',
			'are_postcode' => 'Are Postcode',
			'are_osx'      => 'Are Osx',
			'are_osy'      => 'Are Osy',
			'are_status'   => 'Are Status',
			'are_branch'   => 'Are Branch',
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

		$criteria->compare('are_id', $this->are_id);
		$criteria->compare('are_title', $this->are_title, true);
		$criteria->compare('are_postcode', $this->are_postcode, true);
		$criteria->compare('are_osx', $this->are_osx);
		$criteria->compare('are_osy', $this->are_osy);
		$criteria->compare('are_status', $this->are_status, true);
		$criteria->compare('are_branch', $this->are_branch);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}

	public function getTableSchema()
	{

		$table = parent::getTableSchema();

		$table->columns['are_postcode']->isForeignKey = true;
		$table->foreignKeys['are_postcode']           = array('LinkOfficeToPostcode', 'postcode');

		return $table;
	}
}