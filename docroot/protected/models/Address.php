<?php

/**
 * This is the model class for table "address".
 *
 *
 *
 * The followings are the available columns in table 'address':
 * @property string       $id
 * @property PropertyArea $area
 * @property Property[]   $properties
 * @property Client[]     $clients
 * @property String       $searchString
 */
class Address extends CActiveRecord implements IAddress
{

	public $line1 = '';
	public $line2 = '';
	public $line3 = '';
	public $line4 = '';
	const LINES_COUNT = 5;

	/**
	 * @var string Line5 represents a city or County
	 */
	public $line5 = '';
	public $lat = '';
	public $lng = '';
	public $postcode = '';
	public $postcodeAnywhereID = 0;
	public $searchString = '';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Address the static model class
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

		return 'address';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('postcode', 'required', 'on' => 'addressRequire'),
			array('lat, lng, postcodeAnywhereID', 'numerical', 'allowEmpty' => true, 'integerOnly' => false),
			array(
				'line1, line2, line3, line4, line5', 'length',
				'max' => 255
			),
			array(
				'postcode', 'length',
				'max' => 12
			),
			array('line5', 'required', 'on' => 'addressRequire'),
			array(
				'id, line1, line2, line3, line4, line5, postcode, lat, lng', 'safe',
				'on' => 'search'
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'area'       => [self::BELONGS_TO, 'PropertyArea', 'areaId'],
			'properties' => [self::HAS_MANY, 'Property', 'addressId'],
			'clients'    => [self::HAS_MANY, 'Client', 'addressID'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'       => 'ID',
			'line1'    => 'Line 1',
			'line2'    => 'Line 2',
			'line3'    => 'Line 3',
			'line4'    => 'Line 4',
			'line5'    => 'City or County',
			'postcode' => 'Postcode',
			'lat'      => 'Lat',
			'lng'      => 'Lng',
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
		$criteria->compare('line1', $this->line1, true);
		$criteria->compare('line2', $this->line2, true);
		$criteria->compare('line3', $this->line3, true);
		$criteria->compare('line4', $this->line4, true);
		$criteria->compare('line5', $this->line5, true);
		$criteria->compare('postcode', $this->postcode, true);

		$addressParts = explode(' ', str_replace(",", "", $this->searchString));

		foreach ($addressParts as $part) {
			$criteria = $criteria->compare("concat_ws(' ', line1, line2, line3, line4, line5)", $part, true);
		}

		$criteria->compare("REPLACE(postcode, ' ', '')", str_replace(' ', '', $this->searchString), true, 'OR');

		$criteria->compare('postcode', $this->postcode, true);
		return new CActiveDataProvider($this, CMap::mergeArray(Yii::app()->params['CActiveDataProvider'], array(
																											   'criteria' => $criteria,
																										  )));
	}

	/**
	 *
	 * @param string $separator how to implode lines of address
	 * @return string
	 */
	public function toString($separator = ", ")
	{

		$parts = array();
		if ($this->line1) $parts[] = $this->line1;
		if ($this->line2) $parts[] = $this->line2;
		if ($this->line3) $parts[] = $this->line3;
		if ($this->line4) $parts[] = $this->line4;
		if ($this->line5) $parts[] = $this->line5;
		if ($this->postcode) $parts[] = strtoupper($this->postcode);

		return implode($separator, array_filter($parts));

	}

	protected function beforeSave()
	{

		$this->searchString = trim($this->toString(' '));
		return parent::beforeSave();
	}

	/**
	 * returns a reference to the actual IAdress Object
	 *
	 * helper method that in most cases will return $this. in case of property may return related address object in future.
	 *
	 * @return IAddress returns a reference to the actual IAdress Object
	 */
	public function getAddressObject()
	{

		return $this;
	}

	/**
	 *    returns an array of all lines in the address.
	 *
	 * key should represent line's number.
	 * If address line is not specified should return empty value for corresponding key
	 * i.e.
	 *
	 *  must follow the contract <code>count(IAddress::getAllLines()) == IAddress::getLinesCount()</code>
	 * <code>
	 * $array = Array(
	 *    '1' => 'Line 1',
	 *    '2' => 'Line 2',
	 *    '3' => '',
	 *    '4' => 'Line 4',
	 *    '5' => ''
	 * )
	 * </code>
	 *
	 * @return Array of all lines in the address.
	 */
	public function getAllLines()
	{

		$lines    = array();
		$lines[1] = $this->line1 ? $this->line1 : "";
		$lines[2] = $this->line2 ? $this->line2 : "";
		$lines[3] = $this->line3 ? $this->line3 : "";
		$lines[4] = $this->line4 ? $this->line4 : "";
		$lines[5] = $this->line5 ? $this->line5 : "";
		return $lines;
	}

	/**
	 * returns single line of address which number is $line
	 *
	 * @param $line int number of line  to be returned
	 * @return String returns single line which number is $line
	 *
	 * @throws OutOfBoundsException if $line is less than first line or greater than IAddress::getLinesCount()
	 */
	public function getLine($line)
	{

		if ($line < 0 || $line > $this->getLinesCount()) {
			throw new OutOfBoundsException('Line ' . $line . ' is out of bounds. $line must be between 1 and ' . $this->getLinesCount() . '');
		}
		return $this->getAllLines()[$line];
	}

	/**
	 * returns total number of lines that this IAddress may represent.
	 *
	 * @return int
	 */
	public function getLinesCount()
	{

		return self::LINES_COUNT;
	}

	/**
	 * returns postcode of the address.
	 *
	 * @return String postcode of the address.
	 */
	public function getPostcode()
	{

		return strtoupper($this->postcode);
	}

	/**
	 * returns one part of the postcode.
	 *
	 * Most commonly first part of postcode may be used independently.
	 * Also may be used to retrieve a second part.
	 *
	 * @param int $part which part of the postcode to return. accepts two values <code>IAddress::POSTCODE_POART_ONE</code> or <code>IAddress::POSTCODE_POART_TWO</code>
	 * @throws InvalidArgumentException
	 * @return String requested part of the postcode
	 */
	public function getPostcodePart($part = IAddress::POSTCODE_PART_ONE)
	{

		if (!in_array($part, [IAddress::POSTCODE_PART_ONE, IAddress::POSTCODE_PART_TWO])) {
			throw new InvalidArgumentException('part : ' . $part . ' is not a valid postcode part index');
		}
		return strtoupper($this->postcode ? explode(' ', $this->postcode)[$part] : '');
	}

	/**
	 * returns Latitude of the address if it has one; null otherwise.
	 *
	 * @return Float|null returns Latitude of the address if it has one; null otherwise.
	 */
	public function getLat()
	{

		return $this->lat ? $this->lat : null;
	}

	/**
	 * returns longitude of the address if it has one; null otherwise.
	 *
	 * @return Float|null returns longitude of the address if it has one; null otherwise.
	 */
	public function getLng()
	{

		return $this->lng ? $this->lng : null;
	}

	/**
	 * returns ID of the address in the postcodeAnywhere database if it has one; null otherwise.
	 *
	 * @return int|null returns ID of the address in the postcodeAnywhere database if it has one; null otherwise.
	 */
	public function getPostcodeAnywhereId()
	{

		return $this->postcodeAnywhereID ? $this->postcodeAnywhereID : null;
	}

	/**
	 *
	 * in Address implementation line5 should represent City
	 *
	 * @return String
	 */
	public function getCity()
	{

		return $this->line5;
	}

	/**
	 * @param string $separator
	 * @return String
	 */
	public function getFullAddressString($separator = '<br>')

	{

		return $this->toString($separator);
	}

	/**
	 * @return PropertyArea|null returns Area object fi it exists or null otherwise.
	 */
	public function getAreaObject()
	{

		return $this->area ? $this->area : null;
	}

	public function behaviors()
	{

		return array(
			'createdModifiedBehaviour' => array(
				'class' => 'application.components.behaviours.CreatedModifiedBehavior',
			)
		);
	}

}
