<?php

/**
 * This is the model class for table "Location".
 *
 * The followings are the available columns in table 'Location':
 * @property integer $id
 * @property string  $location
 * @property string  $city
 * @property string  $postcode
 * @property double  $latitude
 * @property double  $longitude
 */
class Location extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Location the static model class
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
		return 'location';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('latitude, longitude', 'numerical'),
			array('city, postcode', 'length', 'max'=> 255),
			array('address', 'safe'),
			array('city', 'required', 'on' => 'locationRequire'),
			array('postcode', 'required', 'on' => 'locationRequire'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, address, city, postcode, latitude, longitude', 'safe', 'on'=> 'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'        => 'ID',
			'address'   => 'Address',
			'city'      => 'City / County',
			'postcode'  => 'Postcode',
			'latitude'  => 'Latitude',
			'longitude' => 'Longitude',
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
		$criteria->compare('address', $this->address, true);
		$criteria->compare('city', $this->city, true);
		$criteria->compare('postcode', $this->postcode, true);
		$criteria->compare('latitude', $this->latitude);
		$criteria->compare('longitude', $this->longitude);

		return new CActiveDataProvider($this, array(
												   'criteria'=> $criteria,
											  ));
	}

	public function getFullLocation($separator = ", ")
	{
		$fullString = array();
		if ($this->address) $fullString[] = $this->address;
		if ($this->city) $fullString[] = $this->city;
		if ($this->postcode) $fullString[] = strtoupper($this->postcode);

		return implode($separator, array_filter($fullString));
	}
}