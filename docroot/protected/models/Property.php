<?php

/**
 * This is the model class for table "property".
 *
 * The followings are the available columns in table 'property':
 * @property integer           $pro_id
 * @property string            $pro_status
 * @property string            $pro_addr1
 * @property string            $pro_addr2
 * @property string            $pro_addr3
 * @property string            $pro_addr4
 * @property string            $pro_addr5
 * @property string            $pro_addr6
 * @property integer           $pro_country
 * @property string            $pro_postcode
 * @property integer           $pro_area
 * @property string            $pro_pcid
 * @property string            $pro_dump
 * @property string            $pro_std
 * @property string            $pro_ward
 * @property string            $pro_authority
 * @property integer           $pro_east
 * @property integer           $pro_north
 * @property string            $pro_latitude
 * @property string            $pro_longitude
 * @property integer           $pro_ptype
 * @property integer           $pro_psubtype
 * @property string            $pro_built
 * @property string            $pro_refurbed
 * @property integer           $pro_floors
 * @property string            $pro_floor
 * @property string            $pro_listed
 * @property string            $pro_parking
 * @property string            $pro_garden
 * @property string            $pro_gardenlength
 * @property integer           $pro_reception
 * @property integer           $pro_bedroom
 * @property integer           $pro_bathroom
 * @property string            $pro_tenure
 * @property string            $pro_leaseend
 * @property string            $pro_location
 * @property string            $pro_timestamp
 * @property string            $servicecharge
 * @property string            $groundrent
 *
 * @property Deal[]            $instructions
 * @property Client[]          $owners
 * @property Client[]          $tenants
 * @property Address           $address
 * @property PropertyArea      $area
 */
class Property extends CActiveRecord implements IAddress
{

	const TENURE_LEASEHOLD         = 'Leasehold';
	const TENURE_FREEHOLD          = 'Freehold';
	const TENURE_SHARE_OF_FREEHOLD = 'Share of Freehold';
	const TENURE_SHARED_OWNERSHIP  = 'Shared Ownership';
	const LISTED_NO                = 'No';
	const LISTED_GRADE_I           = 'Grade I';
	const LISTED_GRADE_II          = 'Grade II';
	const LISTED_GRADE_II_EXTRA    = 'Grade II*';
	const CLIENT_TYPE_OWNER        = 'owner';
	const CLIENT_TYPE_TENANT       = 'tenant';
	const TENANT_TABLE_NAME        = 'currentPropertyTenant';
	const OWNER_TABLE_NAME         = 'currentPropertyOwner';
	const FLOOR_NA                 = 'NA';
	const FLOOR_LOWER_GROUND       = 'Lower Ground';
	const FLOOR_GROUND             = 'Ground';
	const FLOOR_FIRST              = 'First';
	const FLOOR_SECOND             = 'Second';
	const FLOOR_THIRD              = 'Third';
	const FLOOR_FOURTH             = 'Fourth';
	const FLOOR_FIFTH              = 'Fifth';
	const FLOOR_SIXTH              = 'Sixth';
	const FLOOR_SEVENTH            = 'Seventh';
	const FLOOR_EIGHTH             = 'Eighth';
	const FLOOR_NINTH              = 'Ninth';
	const FLOOR_TENTH              = 'Tenth';
	const FLOOR_ELEVENTH           = 'Eleventh';
	const FLOOR_TWELFTH            = 'Twelfth';
	const FLOOR_THIRTEENTH         = 'Thirteenth';
	const FLOOR_FOURTEENTH         = 'Fourteenth';
	const FLOOR_FIFTEENTH          = 'Fifteenth';
	const FLOOR_SIXTEENTH          = 'Sixteenth';
	const FLOOR_SEVENTEENTH        = 'Seventeenth';
	const FLOOR_EIGHTEENTH         = 'Eighteenth';
	const FLOOR_NINETEENTH         = 'Nineteenth';
	const FLOOR_TWENTIETH          = 'Twentieth';

	public $fullAddressString = '';
	public $postcode = '';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Property the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	private static function getClientTypes()
	{

		return array_combine($t = [self::CLIENT_TYPE_OWNER, self::CLIENT_TYPE_TENANT], $t);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'property';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		$propertyTypes = CHtml::listData(PropertyType::model()->findAll(), 'pty_id', 'pty_id');
		return array(

			array('pro_tenure', 'in', 'range' => self::getTenureTypes()),
			array('pro_ptype, pro_psubtype', 'in', 'range' => $propertyTypes),
			array('pro_floor', 'in', 'range' => self::getFloorNames()),
			array('pro_parking', 'in', 'range' => self::getParkigTypes()),
			array('pro_garden', 'in', 'range' => self::getGardenTypes()),
			array('pro_bedroom, pro_reception, pro_bathroom, pro_floors,  pro_country, pro_area', 'numerical', 'integerOnly' => true),
			array('pro_leaseend,groundrent, servicecharge', 'type', 'type' => 'string'),
			array('addressId', 'required', 'message' => 'Property must have an address'),
			array(
				'fullAddressString, pro_id, pro_status, pro_addr1, pro_addr2, pro_addr3, pro_addr4, pro_addr5, pro_addr6, pro_country, pro_postcode, pro_area, pro_pcid, pro_dump, pro_std, pro_ward, pro_authority, pro_east, pro_north, pro_latitude, pro_longitude, pro_ptype, pro_psubtype, pro_built, pro_refurbed, pro_floors, pro_floor, pro_listed, pro_parking, pro_garden, pro_gardenlength, pro_reception, pro_bedroom, pro_bathroom, pro_tenure, pro_leaseend, pro_location',
				'safe', 'on' => 'search'
			),
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
			'instructions'    => [self::HAS_MANY, 'Deal', 'dea_prop', 'order' => 'instructions.dea_created DESC', 'together' => true],
			'area'            => [self::BELONGS_TO, 'PropertyArea', 'pro_area', 'together' => true],
			'owners'          => [self::MANY_MANY, 'Client', 'currentPropertyOwner(propertyId, clientId)'],
			'tenants'         => [self::MANY_MANY, 'Client', 'currentPropertyTenant(propertyId, clientId)'],
			'address'         => [self::BELONGS_TO, 'Address', 'addressId', 'together' => '', 'joinType' => 'inner join'],
			'propertyType'    => [self::BELONGS_TO, 'PropertyType', 'pro_ptype', 'together' => true],
			'propertySubtype' => [self::BELONGS_TO, 'PropertyType', 'pro_psubtype', 'together' => true],
		);

	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'pro_id'           => 'ID',
			'pro_status'       => 'Status',
			'pro_addr1'        => 'Addr1',
			'pro_addr2'        => 'Addr2',
			'pro_addr3'        => 'Addr3',
			'pro_addr4'        => 'Addr4',
			'pro_addr5'        => 'Addr5',
			'pro_addr6'        => 'Addr6',
			'pro_country'      => 'Country',
			'pro_postcode'     => 'Postcode',
			'pro_area'         => 'Area',
			'pro_pcid'         => 'Pcid',
			'pro_dump'         => 'Dump',
			'pro_std'          => 'Std',
			'pro_ward'         => 'Ward',
			'pro_authority'    => 'Authority',
			'pro_east'         => 'East',
			'pro_north'        => 'North',
			'pro_latitude'     => 'Latitude',
			'pro_longitude'    => 'Longitude',
			'pro_ptype'        => 'Ptype',
			'pro_psubtype'     => 'Psubtype',
			'pro_built'        => 'Built',
			'pro_refurbed'     => 'Refurbed',
			'pro_floors'       => 'Floors',
			'pro_floor'        => 'Floor',
			'pro_listed'       => 'Listed',
			'pro_parking'      => 'Parking',
			'pro_garden'       => 'Garden',
			'pro_gardenlength' => 'Gardenlength',
			'pro_reception'    => 'Reception',
			'pro_bedroom'      => 'Bedroom',
			'pro_bathroom'     => 'Bathroom',
			'pro_tenure'       => 'Tenure',
			'pro_leaseend'     => 'Lease Expires',
			'pro_location'     => 'Location',
			'pro_timestamp'    => 'Timestamp',
			'groundrent'       => 'Ground Rent',
			'servicecharge'    => 'Service Charge',
			'addressId'        => 'Address ID',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @param CDbCriteria $criteria
	 * @param bool        $useDefault
	 * @param string      $glue
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(CDbCriteria $criteria = null, $useDefault = true, $glue = 'AND')
	{

		if ($criteria && $useDefault) {
			$glue = $glue == 'AND' ? : false;
			$criteria->mergeWith($this->getSearchCriteria(), $glue);
		} elseif (!$criteria) {
			$criteria = $this->getSearchCriteria();
		}

		$criteria->with = array('address');

		return new CActiveDataProvider($this, CMap::mergeArray(Yii::app()->params['CActiveDataProvider'], array(
																											   'criteria' => $criteria,
																											   'sort'     => array(
																												   'attributes' => array(
																													   '*',
																													   'address.postcode'          => 'address.postcode',
																													   'address.fullAddressString' => array(
																														   'asc'  => "address.searchString ASC",
																														   'desc' => "address.searchString DESC",
																													   ),
																												   )
																											   )
																										  )));
	}

	public function getSearchCriteria()
	{

		$criteria = new CDbCriteria();

		$criteria->with[] = 'address';
		$addressParts     = explode(' ', str_replace(",", "", $this->fullAddressString));

		foreach ($addressParts as $part) {
			if (is_numeric($part)) {
				$part = ' ' . $part . ' ';
			}
			$criteria->compare("CONCAT(' ', address.searchString, ' ')", $part, true);
		}

		$criteria->compare('address.postcode', $this->pro_postcode . '%', true, 'AND', false);
		$criteria->compare('pro_bedroom', $this->pro_bedroom);
		$criteria->compare('pro_reception', $this->pro_reception);

		return $criteria;
	}

	/**
	 * sets an address string; used only for search scenario.
	 *
	 * @param $value
	 */
	public function setFullAddressString($value)
	{

		$this->fullAddressString = $value;
	}

	public function getFirstPostcodePart()
	{

		return $this->getPostcodePart();
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

		return $this->address;
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

		return $this->address->getAllLines();
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

		return $this->address->getLine($line);
	}

	/**
	 * returns total number of lines that this IAddress may represent.
	 *
	 * @return int
	 */
	public function getLinesCount()
	{

		return $this->address->getLinesCount();
	}

	/**
	 * returns postcode of the address.
	 *
	 * @return String postcode of the address.
	 */
	public function getPostcode()
	{

		return $this->address->getPostcode();
	}

	/**
	 * returns one part of the postcode.
	 *
	 * Most commonly first part of postcode may be used independently.
	 * Also may be used to retrieve a second part.
	 *
	 * @param int $part which part of the postcode to return. accepts two values <code>IAddress::POSTCODE_POART_ONE</code> or <code>IAddress::POSTCODE_POART_TWO</code>
	 * @return String requested part of the postcode
	 */
	public function getPostcodePart($part = IAddress::POSTCODE_PART_ONE)
	{

		return $this->address->getPostcodePart($part);
	}

	/**
	 * returns Latitude of the address if it has one; null otherwise.
	 *
	 * @return Float|null returns Latitude of the address if it has one; null otherwise.
	 */
	public function getLat()
	{

		return $this->address->getLat() ? $this->address->getLat() : $this->pro_latitude;
	}

	/**
	 * returns longitude of the address if it has one; null otherwise.
	 *
	 * @return Float|null returns longitude of the address if it has one; null otherwise.
	 */
	public function getLng()
	{

		return $this->address->getLng() ? $this->address->getLng() : $this->pro_longitude;
	}

	/**
	 * returns ID of the address in the postcodeAnywhere database if it has one; null otherwise.
	 *
	 * @return int|null returns ID of the address in the postcodeAnywhere database if it has one; null otherwise.
	 */
	public function getPostcodeAnywhereId()
	{

		return $this->address->getPostcodeAnywhereId();
	}

	/**
	 *
	 *
	 * @return String
	 */
	public function getCity()
	{

		return $this->address->getCity();
	}

	/**
	 * @param string $separator
	 * @return String
	 */
	public function getFullAddressString($separator = ', ')
	{

		$lines   = [$this->fullAddressString];
		$lines[] = $this->getPostcode();
		return implode($separator, array_filter($lines));
	}

	/**
	 * @param string $separator
	 * @return String
	 */
	public function getShortAddressString($separator = ' ', $area = false)
	{
		return $this->getLine(3) . ($area && $this->area ? $separator . $this->area->are_title : "") . ($this->getFirstPostcodePart() ? $separator . $this->getFirstPostcodePart() : "");
	}

	/**
	 * @param string $separator
	 * @return string
	 */
	public function getPropertyRoomString($separator = " ")
	{

		return
				(!$this->pro_bedroom ? : $this->pro_bedroom . " " . ($this->pro_bedroom > 1 ? 'bedrooms' : 'bedroom')) .
				(!$this->pro_reception ? : " , " . $this->pro_reception . " " . ($this->pro_reception > 1 ? 'receptions' : 'reception')) .
				(!$this->pro_bathroom ? : " , " . $this->pro_bathroom . " " . ($this->pro_bathroom > 1 ? 'bathrooms' : 'bathroom'));
	}

	/**
	 * @return PropertyArea|null returns Area object fi it exists or null otherwise.
	 */
	public function getAreaObject()
	{

		return $this->area ? $this->area : null;
	}

	public static function getTenureTypes()
	{

		return array_combine($t = array(
			self::TENURE_LEASEHOLD,
			self::TENURE_FREEHOLD,
			self::TENURE_SHARE_OF_FREEHOLD,
			self::TENURE_SHARED_OWNERSHIP
		), $t);
	}

	/**
	 * @param        $criteria CDbCriteria
	 * @param        $string
	 * @param string $operator
	 * @return void
	 */
	public static function addFullAddressSearchToCriteria($criteria, $string, $operator = 'AND')
	{

		$addressParts = explode(' ', $string);
		$cr           = new CDbCriteria();
		foreach ($addressParts as $key => $part) {
			$cr->addCondition("address.searchString LIKE :propertyAddress" . $key . "");
			$criteria->params['propertyAddress' . $key] = '%' . $part . '%';
		}
		$criteria->addCondition($cr->condition, $operator);
	}

	public function setClients($clients, $clientType = self::CLIENT_TYPE_OWNER, $instantSave = true)
	{

		if (!in_array($clientType, self::getClientTypes())) {
			throw new InvalidArgumentException('Client type must be in list [' . implode(', ', self::getClientTypes()) . '] actual value : ' . $clientType);
		}
		$clientTableName = ($clientType == self::CLIENT_TYPE_TENANT) ? self::TENANT_TABLE_NAME : self::OWNER_TABLE_NAME;
		if ($this->pro_id) {
			$sql = 'DELETE FROM ' . $clientTableName . ' WHERE propertyId = ' . $this->pro_id;
			Yii::app()->db->createCommand($sql)->execute();
			$sql = [];
			foreach ($clients as $value) {
				$sql[] = "('" . $this->pro_id . "', '" . $value . "')";
			}
			if ($sql) {
				$sql = "REPLACE INTO " . $clientTableName . " (propertyId, clientId) VALUES " . implode(',', $sql) . "";
				Yii::app()->db->createCommand($sql)->execute();
			}
		} else {
			$this->attachEventHandler('onAfterSave', function () use ($clients, $clientType) {

				static $run = false;
				if (!$run) {
					$this->setClients($clients, $clientType);
					$run = true;
				}
			});
		}
		if ($clientType == self::CLIENT_TYPE_TENANT) {
			$this->tenants = Client::model()->findAllByPk($clients);
		} else {
			$this->owners = Client::model()->findAllByPk($clients);
		}

	}

	/**
	 * @param string $delimeter
	 * @return string
	 */
	public function getClientNames($clientType = 'owners', $delimeter = ', ')
	{

		$clientsNames = array();
		foreach ($this->$clientType as $client) {
			$clientsNames[] = $client->fullName;
		}
		return implode($delimeter, $clientsNames);

	}

	public static function getFloorNames()
	{

		return array_combine($t = array(
			self::FLOOR_NA,
			self::FLOOR_LOWER_GROUND,
			self::FLOOR_GROUND,
			self::FLOOR_FIRST,
			self::FLOOR_SECOND,
			self::FLOOR_THIRD,
			self::FLOOR_FOURTH,
			self::FLOOR_FIFTH,
			self::FLOOR_SIXTH,
			self::FLOOR_SEVENTH,
			self::FLOOR_EIGHTH,
			self::FLOOR_NINTH,
			self::FLOOR_TENTH,
			self::FLOOR_ELEVENTH,
			self::FLOOR_TWELFTH,
			self::FLOOR_THIRTEENTH,
			self::FLOOR_FOURTEENTH,
			self::FLOOR_FIFTEENTH,
			self::FLOOR_SIXTEENTH,
			self::FLOOR_SEVENTEENTH,
			self::FLOOR_EIGHTEENTH,
			self::FLOOR_NINETEENTH,
			self::FLOOR_TWENTIETH
		), $t);
	}

	public static function getParkigTypes()
	{

		$t = array('None', 'Street (unallocated)', 'Street (allocated)', 'Off-Street', 'Secure Off-Street', 'Garage');
		return array_combine($t, $t);
	}

	public static function getGardenTypes()
	{

		$t = array('None', 'Private', 'Shared', 'Communal', 'Roof Terrace', 'Balcony');
		return array_combine($t, $t);

	}

	public function setAddress(Address $address)
	{

		$this->address   = $address;
		$this->addressId = $address->id;
	}

	public function updateInstructions()
	{

		foreach ($this->instructions as $instruction) {
			if ($instruction->statusInList(Deal::getActiveStatuses())) {
				$instruction->importFromProperty($this);
				$instruction->save(false);
			}
		}
	}

	public function afterSave()
	{

		$this->updateInstructions();
		parent::afterSave();
	}
}