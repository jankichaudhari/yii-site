<?php

/**
 * This is the model class for table "link_office_to_postcode".
 *
 * The followings are the available columns in table 'link_office_to_postcode':
 * @property integer        $officeId
 * @property string         $postcode
 * @property PropertyArea[] $areas
 */
class LinkOfficeToPostcode extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LinkOfficeToPostcode the static model class
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
		return 'link_office_to_postcode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			['officeId', 'numerical', 'integerOnly' => true],
			['postcode', 'length', 'max' => 10],
			['officeId, postcode', 'safe', 'on' => 'search'],
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
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
			'officeId' => 'Office',
			'postcode' => 'Postcode',
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

		$criteria->compare('officeId', $this->officeId);
		$criteria->compare('postcode', $this->postcode, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}

	public function getPostcodeList($officeId = 0)
	{
		$officePostcodes = self::model()->findAll();
		if ($officeId) {
			$officePostcodes = self::model()->findAllByAttributes(['officeId' => $officeId]);
		}
		$result = [];
		foreach ($officePostcodes as $officePostcode) {
			array_push($result, $officePostcode->postcode);
		}
		return $result;
	}

}