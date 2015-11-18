<?php

/**
 *
 * This is the model class for table "client".
 * The followings are the available columns in table 'client':
 *
 * @property integer                            $cli_id
 * @property string                             $cli_status
 * @property string                             $cli_method
 * @property integer                            $cli_source
 * @property string                             $cli_preferred
 * @property string                             $cli_created
 * @property string                             $cli_reviewed
 * @property string                             $cli_sales
 * @property string                             $cli_lettings
 * @property integer                            $cli_regd
 * @property integer                            $cli_branch
 * @property integer                            $cli_neg
 * @property integer                            $cli_solicitor
 * @property string                             $cli_salutation
 * @property string                             $cli_fname
 * @property string                             $cli_sname
 * @property integer                            $cli_pro
 * @property string                             $cli_email
 * @property string                             $cli_saleemail
 * @property string                             $cli_letemail
 * @property string                             $cli_saleptype
 * @property string                             $cli_salereq
 * @property string                             $cli_letptype
 * @property string                             $cli_letreq
 * @property string                             $cli_area
 * @property string                             $cli_oldnotes
 * @property string                             $cli_oldaddr
 * @property integer                            $cli_salestatus
 * @property integer                            $cli_letstatus
 * @property string                             $cli_mortgagestatus
 * @property string                             $cli_timestamp
 * @property string                             $cli_deposit
 * @property string                             $cli_selling
 * @property string                             $cli_renting
 * @property string                             $cli_wparents
 * @property string                             $cli_valuation
 * @property string                             $cli_workstatus
 * @property string                             $cli_student
 * @property string                             $cli_moveby
 * @property integer                            $secondAddressID
 * @property integer                            $addressID
 * @property integer                            $budget         budget can now be set to null and this is default value meaning that users forgot to specify it. any number (even 0) means some budget
 * @property String                             $secondaryEmail replaces cli_web
 * @property String                                                                       $email          Alias for getEmail() method. read only
 *
 *** Relations
 * @property Property[]                         $properties
 * @property LinkClientToInstruction[]          $dealIds
 * @property Telephone[]                        $telephones
 * @property Note[]                             $notes
 * @property Address                            $address
 * @property Address                            $secondAddress
 * @property User                               $registrator
 * @property Office                             $matchingOffices
 * @property Feature[]                          $features
 * @property ClientStatus                       $saleStatus
 * @property datetime                           $lastContacted
 * @property Sms[]                              $textMessages
 * @property ClientToPostcode                   $matchingPostcodes
 * @property PropertyType[]                     $propertyTypes
 * @property PropertyCategory[]                 $propertyCategories
 * @property Deal[]                                                                       $instructions
 */
class Client extends CActiveRecord implements IHasAddress
{
	const REGISTERED_SALES     = 'Yes';
	const NOT_REGISTERED_SALES = 'No';

	const REGISTERED_LETTINGS     = 'Yes';
	const NOT_REGISTERED_LETTINGS = 'No';

	const EMAIL_SALES_YES = 'Yes';
	const EMAIL_SALES_NO  = 'No';

	const EMAIL_LETTINGS_YES = 'Yes';
	const EMAIL_LETTINGS_NO  = 'No';
	const INFINITE_BEDROOMS  = 999;

	/**
	 * @var string Minimum number of beds for lettings preferences
	 */
	const PROPERTY_TYPE_SALES    = 'sales';
	const PROPERTY_TYPE_LETTINGS = 'lettings';
	const INFINITE_PRICE         = 999999999;
	const METHOD_WEBSITE         = 'Website';
	const METHOD_INTERNET        = 'Internet';
	const METHOD_TELEPHONE       = 'Telephone';
	const METHOD_EMAIL           = 'Email';
	const METHOD_WALK_IN         = 'Walk-in';
	const METHOD_IMPORT          = 'Import';

	const DEFAULT_CLIENT_TYPE_SALES = "Yes";
	const INVALID_EMAIL             = 1;

	public $cli_letbed = '';
	/**
	 * @var string Minimum number of beds for sales preferences
	 */
	public $cli_salebed = '';
	/**
	 * @var string minimum price for sales preferences
	 * @deprecated
	 */
	public $cli_salemin = '';
	/**
	 * @var string maximum price for sales preferences
	 * @deprecated
	 */
	public $cli_salemax = '';
	/**
	 * @var string should not have default value
	 */
	public $cli_saleemail = 'Yes';
	/**
	 * @var string minimum price for lettings preferences
	 * @deprecated
	 */
	public $cli_letmin = '';
	/**
	 * @var string maximum price for lettings preferences
	 * @deprecated
	 */
	public $cli_letmax = '';
	/**
	 * @var int Clients Address id by default is equals to -1 meaning that we can't save client. (address MUST be set)
	 */
	public $addressID = -1;

	public $cli_salestatus = '';

	public $cli_sales = '';
	/**
	 * @var string
	 * @deprecated
	 */
	public $cli_lettings = '';

	/**
	 * @var string
	 * @deprecated
	 */
	public $cli_web = '';
	/**
	 * @var string Clients full name (concatenated name & surname without solutation)
	 */
	protected $fullName = '';
	/**
	 * @var array an array of new phones to be added
	 */
	public $_newPhones = [];

	public $cli_neg = '';
	public $cli_branch = '';

	/**
	 * used for search criteria;
	 * @var int
	 */
	public $minBedrooms = '';
	public $maxBedrooms = '';
	/**
	 * @var string
	 */
	public $minPrice = '';
	/**
	 * @var string
	 */
	public $maxPrice = '';

	public $searchNoBudget = false;
	public $searchNoMinimumBeds = false;

	public $searchPrice = null;
	public $searchExact = false;

	public $feature = [];
	/**
	 * @var array
	 * @deprecated due to incredibly bad naming. There is a relation called matchingPostcodes with s. impossible to notice.
	 *
	 */
	public $matchingPostcode = [];

	public $searchPostcodes = [];
	public $invalidEmail = null;

	private $_propertyTypesIds = [];
	private $_propertyCategoryIds = [];

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Client the static model class
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

		return 'client';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
				['cli_fname, cli_sname, cli_email', 'required'],
				['telephones', 'checkTelephones', 'on' => 'validPhoneOnInsert'],
				['addressID', 'checkAddressID'],
				['propertyCategoryIds, propertyTypesIds', 'safe'],
				['cli_salemin, cli_salemax', 'numerical', 'allowEmpty' => true, 'on' => 'search'],
				['budget', 'numerical', 'allowEmpty' => true],
				['budget', 'default', 'setOnEmpty' => true, 'value' => null],
				[
						'cli_salebed, cli_source,cli_neg,cli_branch, cli_salestatus, searchPrice',
						'numerical',
						'allowEmpty' => true
				],
				['cli_email, secondaryEmail', 'email'],
				['cli_saleemail, cli_sales', 'in', 'range' => [Client::EMAIL_SALES_YES, Client::EMAIL_SALES_NO]],
				['cli_fname, cli_sname', 'type', 'type' => 'string'],
				['cli_salutation', 'in', 'range' => Client::getSalutationTypes()],
				['cli_preferred', 'in', 'range' => CLient::getContactMethods()],
				['lastContacted', 'default', 'setOnEmpty' => true, 'value' => null],
				['minPrice, maxPrice', 'numerical', 'allowEmpty' => true, 'on' => 'search'],
				['searchNoBudget, searchNoMinimumBeds', 'boolean', 'allowEmpty' => true, 'on' => 'search'],
				['matchingPostcode', 'unsafe'],
				['invalidEmail', 'in', 'range' => [0, 1]],
				['invalidEmail', 'default', 'setOnEmpty' => 0, 'value' => 0],
				['searchPostcodes', 'safe', 'on' => 'search'],
				[
						'fullName, cli_id, cli_status, cli_method,
						cli_source, cli_preferred, cli_created, cli_reviewed,
						cli_vendor, cli_landlord, cli_sales, cli_lettings,
						cli_regd, cli_branch, cli_neg,
						cli_solicitor, cli_salutation,
						cli_fname, cli_sname, cli_pro,
						cli_email, cli_password,
						cli_question, cli_answer, cli_salemin,
						cli_salemax, cli_salebed, cli_saleemail,  cli_saleptype, cli_salereq,
						cli_letptype, cli_letreq, cli_area, cli_oldnotes, cli_oldaddr,
						cli_salestatus, cli_mortgagestatus,
						cli_timestamp, cli_deposit, cli_selling, cli_renting,
						 cli_wparents, cli_valuation, cli_workstatus, cli_student, cli_moveby',
						'safe',
						'on' => 'search'
				],
		);
	}

	/**
	 * Primary addresses validator
	 * @param $field
	 */
	public function checkAddressID($field)
	{

		if ($this->$field == -1) {
			$this->addError($field, 'Primary Address must be selected');
		}
	}

	public function onBeforeValidate($event)
	{

		$this->cli_email = trim($this->cli_email) ? : null;
		$this->cli_fname = trim($this->cli_fname) ? : null;
		$this->cli_sname = trim($this->cli_sname) ? : null;
		parent::onBeforeValidate($event);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
				'telephones'         => [self::HAS_MANY, 'Telephone', 'tel_cli', 'order' => "tel_ord ASC"],
				'address'            => [self::BELONGS_TO, 'Address', 'addressID', "together" => true],
				'secondAddress'      => [self::BELONGS_TO, 'Address', 'secondAddressID', "together" => true],
				'registrator'        => [self::BELONGS_TO, 'User', 'cli_regd', 'together' => true],
				'negotiator'         => [self::BELONGS_TO, 'User', 'cli_neg', 'together' => true],
				'branch'             => [self::BELONGS_TO, 'Branch', 'cli_branch', "together" => true],
				'features'           => [self::MANY_MANY, 'Feature', 'link_client_to_feature(clientId, featureId)'],
				'featuresLinks'      => [self::HAS_MANY, 'LinkClientToFeature', 'clientId'],
				'matchingPostcodes'  => [self::HAS_MANY, 'ClientToPostcode', 'clientId'],
				'properties'         => [self::MANY_MANY, 'Property', 'currentPropertyOwner(clientId, propertyId)'],
				'saleStatus'         => [self::BELONGS_TO, 'ClientStatus', 'cli_salestatus', 'together' => true],
				'source'             => [self::BELONGS_TO, 'Source', 'cli_source', "together" => true],
				'textMessages'       => [self::HAS_MANY, 'Sms', 'clientId', 'order' => 'textMessages.created'],
				'propertyTypes'      => [self::MANY_MANY, 'PropertyType', 'link_client_to_propertyType(clientId,typeId)'],
				'propertyCategories' => [self::MANY_MANY, 'PropertyCategory', 'link_client_to_propertyCategory(clientId,categoryId)'],
				'viewings'           => array(
						self::MANY_MANY,
						'Appointment',
						'cli2app(c2a_cli, c2a_app)',
						'select' => ['*'],
						'on'     => 'viewings.app_type = "' . Appointment::TYPE_VIEWING . '"',
						'with'   => ['user' => ['together' => true], 'instructions' => ['together' => true]],
						'order'  => 'viewings.app_start DESC'
				),
				'notes'              => [
						self::HAS_MANY,
						'Note',
						'not_row',
						'on'    => "notes.not_type = 'client_general'",
						'order' => 'notes.not_date DESC'
				],
				'instructions' => [
						self::MANY_MANY,
						'Deal',
						'link_client_to_instruction(clientId,dealId)',
						'on' => 'instructions_instructions.capacity = "Owner"'
				],

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
				'cli_id'             => 'ID',
				'cli_status'         => 'Status',
				'cli_method'         => 'Method',
				'cli_source'         => 'Source',
				'cli_preferred'      => 'Preferred',
				'cli_created'        => 'Created',
				'cli_reviewed'       => 'Reviewed',
				'cli_sales'          => 'Sales',
				'cli_lettings'       => 'Lettings',
				'cli_regd'           => 'Regd',
				'cli_branch'         => 'Branch',
				'cli_neg'            => 'Neg',
				'cli_solicitor'      => 'Solicitor',
				'cli_salutation'     => 'Salutation',
				'cli_fname'          => 'First name',
				'cli_sname'          => 'Surname',
				'cli_pro'            => 'Property ID',
				'cli_email'          => 'Email',
				'cli_web'            => 'Website',
				'cli_salemin'        => 'Salemin',
				'cli_salemax'        => 'Salemax',
				'cli_salebed'        => 'Minimum beds',
				'cli_saleemail'      => 'Saleemail',
				'cli_letmin'         => 'Letmin',
				'cli_letmax'         => 'Letmax',
				'cli_letbed'         => 'Minimum beds',
				'cli_letemail'       => 'Letemail',
				'cli_saleptype'      => 'Saleptype',
				'cli_salereq'        => 'Salereq',
				'cli_letptype'       => 'Letptype',
				'cli_letreq'         => 'Letreq',
				'cli_area'           => 'Area',
				'cli_oldnotes'       => 'Oldnotes',
				'cli_oldaddr'        => 'Oldaddr',
				'cli_salestatus'     => 'Salestaus',
				'cli_letstatus'      => 'Letstatus',
				'cli_mortgagestatus' => 'Mortgagestatus',
				'cli_timestamp'      => 'Timestamp',
				'cli_deposit'        => 'Deposit',
				'cli_selling'        => 'Selling',
				'cli_renting'        => 'Renting',
				'cli_wparents'       => 'Wparents',
				'cli_valuation'      => 'Valuation',
				'cli_workstatus'     => 'Workstatus',
				'cli_student'        => 'Student',
				'cli_moveby'         => 'Moveby',
				'addressID'          => 'Address Id',
				'secondAddressID'    => 'Correspondence Address Id',
				'lastContacted'      => 'Last Contacted',
				'secondaryEmail'     => 'Secondary Email',

		);
	}

	/**
	 * @param CDbCriteria $criteria
	 * @return CDbCriteria
	 */
	private function searchCriteria(CDbCriteria $criteria = null)
	{

		$criteria = $criteria ? clone $criteria : new CDbCriteria();

		$criteria->compare('cli_sales', $this->cli_sales);
		$criteria->compare('cli_created', '>=' . $this->cli_created);
		$criteria->compare('cli_neg', $this->cli_neg);
		$criteria->compare('cli_branch', $this->cli_branch);
		$criteria->compare('cli_salestatus', $this->cli_salestatus);
		$criteria->compare('cli_saleemail', $this->cli_saleemail);
		$criteria->compare('invalidEmail', $this->invalidEmail);
		$criteria->compare('cli_salebed', '<=' . $this->cli_salebed);

		$criteria->with = ['registrator', 'address', 'telephones'];
		/**
		 * Searching with telephone was extremely slow. using together with MANY_MANY or HAS_MANY is very slow.
		 */
		if ($this->telephones) {
			$phone = $this->telephones[0];

			if ($phone && strlen($phone->getPlainNumber()) >= 5 && $number = $phone->getPlainNumber()) {
				$clientsIds = $phone->getClientsWithSimilarPhone();
				$clientsIds = !$clientsIds ? 0 : $clientsIds;
				$criteria->compare('cli_id', $clientsIds);
			}
		}

		if ($this->fullName) {
			$parts       = explode(" ", $this->fullName);
			$clientName  = [];
			$clientEmail = [];
			foreach ($parts as $key => $part) {
				$clientName[]                     = 'cli_fname LIKE :part' . $key . ' OR cli_sname LIKE :part' . $key;
				$clientEmail[]                    = 'cli_email LIKE :part' . $key;
				$criteria->params[':part' . $key] = $part . '%';
			}
			$criteria->addCondition('((' . implode(') AND (', $clientName) . ')) OR ((' . implode(') AND (', $clientEmail) . '))');
		}

		if ($this->minPrice || $this->maxPrice) {
			$criteria->addCondition("budget BETWEEN " . ($this->minPrice ? : 0) . " AND " . ($this->maxPrice ? : self::INFINITE_PRICE) . ($this->searchNoBudget ? ' OR budget is null' : ''));
		}

		if ($propertyTypes = $this->getPropertyTypesIds()) {
			$criteria->join .= ' LEFT JOIN link_client_to_propertyType as l_propertyTypes ON l_propertyTypes.clientId = t.cli_id';
			$criteria->addCondition('l_propertyTypes.typeId IS NULL OR l_propertyTypes.typeId IN (' . implode(', ', $propertyTypes) . ')');

		}

		if ($this->searchPostcodes) {
			$criteria->join .= ' LEFT JOIN link_client_to_postcode as l_postcodes ON l_postcodes.clientId = t.cli_id';
			$criteria->addCondition("l_postcodes.postcode IS NULL OR l_postcodes.postcode in ('" . implode("','", $this->searchPostcodes) . "')");
		}

		$criteria->join .= ' LEFT JOIN link_client_to_propertyCategory as l_propCategory ON l_propCategory.clientId = t.cli_id';
		$propertyCategoryCondition = "l_propCategory.categoryId IS NULL";
		if ($propertyCategories = $this->getPropertyCategoryIds()) {
			$propertyCategoryCondition .= ' OR l_propCategory.categoryId IN (' . implode(', ', $propertyCategories) . ')';
		}
		$criteria->addCondition($propertyCategoryCondition);

		$criteria->group = 't.cli_id';
		return $criteria;
	}

	private function searchSortCriteria(CSort $sort = null)
	{

		$sort               = $sort ? clone $sort : new CSort();
		$sort->defaultOrder = 'cli_created DESC';
		$sort->attributes   = array(
				'fullName'    => array(
						'asc'  => 'cli_fname, cli_sname',
						'desc' => 'cli_fname DESC, cli_sname DESC',
				),
				'registrator' => array(
						'asc'  => 'registrator.use_fname ASC',
						'desc' => 'registrator.use_fname DESC',
				),
				'*',
		);

		return $sort;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @param CDbCriteria $criteria
	 * @param CSort       $sort
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(CDbCriteria $criteria = null, CSort $sort = null)
	{

		$criteria = $this->searchCriteria($criteria);
		$sort     = $this->searchSortCriteria($sort);
		return new CActiveDataProvider($this, CMap::mergeArray(Yii::app()->params['CActiveDataProvider'],
															   array(
																	   'criteria'      => $criteria,
																	   'sort'          => $sort,
																	   'countCriteria' => array(
																			   'condition' => $criteria->condition,
																			   'params'    => $criteria->params,
																			   'join'      => $criteria->join,
																			   'distinct'  => true,
																	   ),
															   )
		));
	}

	/**
	 * This is a one crazy-ass method.
	 *
	 * @param string $criteriaType
	 * @return int
	 */
	public function  getTotalViewings($criteriaType = '')
	{

		$total = 0;

		foreach ($this->viewings as $viewing) {
			switch ($criteriaType) {
				case 'cancelled' :
					if ($viewing->app_status == Appointment::STATUS_CANCELLED) {
						$total++;
					}
					break;
				case 'upcoming' :
					if ($viewing->app_start > date('Y-m-d H:i:s') && ($viewing->app_status == Appointment::STATUS_ACTIVE)) {
						$total++;
					}
					break;
				case 'finished' :
					if (($viewing->app_start <= date('Y-m-d H:i:s')) && ($viewing->app_status == Appointment::STATUS_ACTIVE)) {
						$total++;
					}
					break;
				case 'deleted' :
					if ($viewing->app_status == Appointment::STATUS_DELETED) {
						$total++;
					}
					break;
				default :
					if ($viewing->app_status != Appointment::STATUS_DELETED) {
						$total++;
					}
					break;
			}
		}
		return $total;
	}

	/**
	 * Returns number of client's in pending status
	 * @param string $scope
	 * @return int
	 */
	public function pendingCount($scope = 'sales')
	{

		switch (strtolower($scope)) {
			case 'sales' :
				$status = 'Pending_New_Client_Sales';
				break;
			case 'lettings' :
				$status = 'Pending_New_Client_Lettings';
				break;
			default :
				$status = 'Pending_New_Client_Sales';
		}
		$sql   = "SELECT COUNT(*) as clientCount FROM " . $this->tableName() . " WHERE cli_status = '" . $status . "'";
		$count = Yii::app()->db->createCommand($sql)->queryScalar(array($status));
		return $count ? : 0;
	}

	/**
	 * @param bool $withSalutation
	 * @return String
	 */
	public function getFullName($withSalutation = false)
	{

		$fullName = trim($this->cli_fname . ' ' . $this->cli_sname);
		if ($withSalutation) {
			$fullName = $this->cli_salutation . " " . $fullName;
		}
		return $fullName;
	}

	/**
	 * This method only shoud be used for search scenarious because it does not set clients name or surname
	 * Mainly it is just a search string to look for in DB
	 *
	 * @param String $fullName
	 * @return $this
	 */
	public function setFullName($fullName)
	{

		$this->fullName = $fullName;
		return $this;
	}

	/**
	 * Returns clients whose telephone number is a partial march from THE END OF THE NUMBER
	 * !!IT SEARCHES FROM THE RIGHT SIDE!!
	 *
	 * @param $phone
	 * @return static[]
	 */
	public function findAllByPhone($phone)
	{

		$criteria       = $this->getDbCriteria();
		$criteria->with = ['telephones'];
		$criteria->addCondition('telephones.plainNumberReversed LIKE "' . strrev($phone) . '%"');
		return $this->findAll($criteria);
	}

	/**
	 * @param $phone
	 * @return null|static
	 */
	public function findByPhone($phone)
	{

		$criteria        = $this->getDbCriteria();
		$criteria->limit = 1;
		$clients         = $this->findAllByPhone($phone);
		return isset($clients[0]) ? $clients[0] : null;
	}

	/**
	 * @param     $search
	 * @param int $limit
	 * @return static[]
	 */
	public function quickSearch($search, $limit = 5)
	{

		$criteria = $this->getDbCriteria();

		if (strpos($search, '@') === false) {
			$criteria->addCondition('cli_fname like :search OR cli_sname like :search');
		} else {
			$criteria->addCondition('cli_email like :search');
		}

		$criteria->params['search'] = $search . '%';
		if ($limit) {
			$criteria->limit = 5;
		}

		return $this->findAll($criteria);

	}

	/**
	 * Client must have at least one phone
	 */
	protected function afterConstruct()
	{

		parent::afterConstruct();
	}

	/**
	 * @return bool
	 */
	protected function beforeValidate()
	{

		$this->cli_salemin = preg_replace('/[^0-9.]/', '', $this->cli_salemin);
		$this->cli_salemax = preg_replace('/[^0-9.]/', '', $this->cli_salemax);
		$this->budget      = preg_replace('/[^0-9.]/', '', $this->budget);
		return parent::beforeValidate();
	}

	/**
	 * @return bool
	 */
	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->cli_created = date("Y-m-d H:i:s");
			$this->cli_regd    = Yii::app()->user->getId();
		} else {
			if (!$this->cli_regd) {
				$this->cli_regd = Yii::app()->user->getId();
			}
		}
		$this->cli_sales = self::DEFAULT_CLIENT_TYPE_SALES;
		return parent::beforeSave();
	}

	/**
	 * Telephones field's validator
	 *
	 * @return bool
	 */
	public function checkTelephones()
	{

		$phones = array_merge($this->telephones, $this->_newPhones);
		foreach ($phones as $key => $phone) {
			if (!$phone->tel_number) {
				unset($phones[$key]);
			}
		}

		if (!$phones) {
			$this->addError('telephones', 'Client must have at least one phone number');
			return false;
		}

		return true;
	}

	public function hasPhone($phoneId)
	{

		static $phoneIds;
		if (!$phoneIds) {
			foreach ($this->telephones as $value) {
				if ($value->tel_id) $phoneIds[] = $value->tel_id;
			}
		}
		return in_array($phoneId, $phoneIds);
	}

	/**
	 * @param $instructionType
	 * @param $types
	 * @return Client
	 */
	public function setPropertyTypes($instructionType, $types)
	{

		if ($instructionType == 'sales') {
			$this->cli_saleptype = implode('|', $types);

		} else {
			$this->cli_letptype = implode('|', $types);
		}
		return $this;
	}

	/**
	 * @param $instructionType
	 * @return array
	 */
	public function getPropertyTypes($instructionType = 'sales')
	{

		if ($instructionType == 'sales') {
			return explode('|', $this->cli_saleptype);

		} else {
			return explode('|', $this->cli_letptype);
		}
	}

	/**
	 * @return array
	 */
	public function getMatchingPostcodes()
	{

		$result = [];
		foreach ($this->matchingPostcodes as $value) {
			$result[$value->postcode] = $value->postcode;
		}
		return $result;
	}

	/**
	 * @param $postcode
	 * @return bool
	 */
	public function clientBelongsToPostcode($postcode)
	{

		static $postcodesCache;
		if ($postcodesCache === null) {
			$postcodesCache = array();
			foreach ($this->matchingPostcodes as $value) {
				$postcodesCache[] = $value->postcode;
			}
		}
		return in_array($postcode, $postcodesCache);
	}

	/**
	 * Returns possible contact methods for client
	 * @return array
	 */
	public static function getContactMethods()
	{

		return array('Email' => 'Email', 'Telephone' => 'Telephone', 'Post' => 'Post');
	}

	/**
	 * Returns possible salutation types for client
	 * @return array
	 */
	public static function getSalutationTypes()
	{

		return Array(
				'Mr.'   => 'Mr.',
				'Mrs.'  => 'Mrs.',
				'Miss.' => 'Miss.',
				'Ms.'   => 'Ms.',
				'Dr.'   => 'Dr.',
				'Rev.'  => 'Rev.',
				'Lord'  => 'Lord',
				'Lady'  => 'Lady',
				'Prof.' => 'Prof.'
		);
	}

	/**
	 * @return array
	 */
	public static function getEmailNotifyOptions()
	{

		return array(self::EMAIL_SALES_YES => self::EMAIL_SALES_YES, self::EMAIL_SALES_NO => self::EMAIL_SALES_NO);
	}

	/**
	 * Returns ActiveDataProvider for clients that match passed Instruction
	 *
	 * @param Deal        $instruction instruction against which should be performed matching
	 * @param CDbCriteria $criteria    additional criteria to be applied to the search
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return CActiveDataProvider ActiveDataProvider representing matched clients
	 */
	public function matchByInstruction(Deal $instruction, CDbCriteria $criteria = null)
	{

		if ($instruction === null) {
			throw new InvalidArgumentException("instruction");
		}

		$criteria = new CDbCriteria($criteria);
		if ($instruction->dea_type == Deal::TYPE_SALES) {
			$criteria->compare('cli_salebed', "<=" . $instruction->dea_bedroom);
			$criteria->compare('cli_salemin', "<=" . round($instruction->dea_marketprice * 1.1));
			$criteria->compare('cli_salemax', ">=" . round($instruction->dea_marketprice * 0.9));
			$criteria->addSearchCondition("CONCAT('|',cli_saleptype,'|')", $instruction->dea_ptype);
			$criteria->addSearchCondition("CONCAT('|',cli_saleptype,'|')", $instruction->dea_psubtype, true, 'OR');
		}
	}

	public function hasAddress()
	{

		return (bool)$this->address;
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
	 * @param Deal $instruction
	 * @return CActiveDataProvider
	 * @deprecated
	 */
	public function searchAgainstInstruction(Deal $instruction)
	{

		$criteria = $this->getDbCriteria();

		if ($instruction) {

			$criteria->compare('cli_sales', 'Yes');
			$criteria->compare('cli_saleemail', $this->cli_saleemail);
			$criteria->addCondition('(cli_salebed >= :bedroomNum OR cli_salebed = 0)');
			$criteria->addCondition('cli_salemin <= :minPrice OR cli_salemin = 0');
			$criteria->addCondition('cli_salemax >= :maxPrice OR cli_salemax = 0');
			$criteria->addCondition('cli_salemax > 0');
			$criteria->addCondition('cli_salemin > 0');

			$criteria->params['bedroomNum'] = $instruction->dea_bedroom;
			$criteria->params['minPrice']   = $instruction->dea_marketprice * 1.1;
			$criteria->params['maxPrice']   = $instruction->dea_marketprice * 0.9;

			$instruction->dea_ptype = (array)$instruction->dea_ptype;
		}

		return new CActiveDataProvider($this, Array(
				'criteria'   => $criteria,
				'pagination' => ['pageSize' => 200,],
				'sort'       => array(
						'defaultOrder' => 'cli_created DESC',
						'attributes'   => array(
								'*',
								'fullName' => [
										'asc'  => 'concat(cli_fname, cli_sname) ASC',
										'desc' => 'concat(cli_fname, cli_sname) DESC'
								]
						),
				)
		));

	}

	public function clientBelongsToFeature($featureId)
	{

		static $featuresCache;
		if ($featuresCache === null) {
			$featuresCache = array();
			foreach ($this->features as $feature) {
				$featuresCache[] = $feature->fea_id;
			}
		}
		return in_array($featureId, $featuresCache);
	}

	/**
	 * @param $features
	 * @return int
	 * @throws Exception
	 */
	public function saveFeatures($features)
	{

		if ($this->isNewRecord) {
			throw new Exception('Cant save features  for the new Client record');
		}

		$sql    = 'DELETE FROM link_client_to_feature WHERE clientId = ' . $this->cli_id;
		$result = Yii::app()->db->createCommand($sql)->execute();

		if ($features) {
			$sql = [];
			foreach ($features as $featureId => $featureStatus) {
				$sql[] = '(' . $this->cli_id . ',' . $featureId . ', "' . $featureStatus . '")';
			}
			$sql    = 'INSERT INTO link_client_to_feature (clientId, featureId, status) VALUES ' . implode(',', $sql);
			$result = Yii::app()->db->createCommand($sql)->execute();
		}

		return $result;
	}

	public function saveAreas($areas)
	{

		if ($this->isNewRecord) {
			throw new Exception('Cant save areas for the new Client record');
		}
		$query = 'DELETE FROM link_client_to_postcode WHERE clientId = :clientId';
		Yii::app()->db->createCommand($query)->execute(['clientId' => $this->cli_id]);

		if ($areas) {
			$sql = [];
			foreach ($areas as $postcode) {
				$sql[] = '(' . $this->cli_id . ', "' . $postcode . '")';
			}
			$sql = 'INSERT INTO link_client_to_postcode (clientId, postcode) VALUES ' . implode(',', $sql);
			return Yii::app()->db->createCommand($sql)->execute();
		}
		return true;
	}

	public function newlyRegistered()
	{

		$criteria = new CDbCriteria();

		$criteria->compare('cli_neg', '0');
		$criteria->compare('cli_created', '>=' . date('Y-m-d', strtotime(' -1 week')));

		$criteria->compare('cli_branch', $this->cli_branch);
		return new CActiveDataProvider($this, CMap::mergeArray(array(
																	   'criteria' => $criteria,
																	   'sort'     => ['defaultOrder' => 'cli_created DESC']
															   ), Yii::app()->params['CActiveDataProvider']));

	}

	public function getPropertyTypesIds($update = false)
	{

		if ($this->_propertyTypesIds && !$update) return $this->_propertyTypesIds;
		foreach ($this->propertyTypes as $value) {
			$this->_propertyTypesIds[] = $value['pty_id'];
		}
		return $this->_propertyTypesIds;

	}

	/**
	 *
	 * I like magic. Don't know if it's good but basically idea is that when we create a new client property types will be saved onAfterSave event
	 *
	 * added some extra magic. propertyTypes will not be set to. should not be used that way though
	 *
	 * @param $value
	 * @return bool|int
	 */
	public function setPropertyTypesIds($value)
	{

		$value                   = array_filter($value);
		$this->_propertyTypesIds = $value;
		if ($this->getScenario() === 'search') return;

		$func = function () use ($value) {

			Yii::app()->db->createCommand('DELETE FROM link_client_to_propertyType WHERE clientId = :clientId')->execute(['clientId' => $this->cli_id]);
			$sql = [];
			foreach ($value as $type) {
				$sql[] = "({$this->cli_id}, {$type})";
			}

			if ($sql) {
				$sql = "REPLACE INTO link_client_to_propertyType (clientId, typeId) VALUES" . implode(', ', $sql);
				return Yii::app()->db->createCommand($sql)->execute();
			}
			$this->propertyTypes = $this->getRelated('propertyTypes', true);
		};

		if (!$this->cli_id) {
			$this->attachEventHandler('onAfterSave', $func);
		} else {
			$func();
		}

	}

	public function getPropertyCategoryIds($update = null)
	{
		if (!$this->_propertyCategoryIds || $update) {
			foreach ($this->propertyCategories as $value) {
				$this->_propertyCategoryIds[] = $value['id'];
			}
		}
		return $this->_propertyCategoryIds;
	}

	public function setPropertyCategoryIds($value)
	{
		$value                      = array_filter((array)$value);
		$this->_propertyCategoryIds = $value;
		if ($this->getScenario() === 'search') return;

		$callback = function () {
			$sql = "DELETE FROM link_client_to_propertyCategory WHERE clientId = ?";
			Yii::app()->db->createCommand($sql)->execute([$this->cli_id]);
			$sql = $params = [];
			foreach ($this->_propertyCategoryIds as $category) {
				$sql[]    = '(?, ?)';
				$params[] = $this->cli_id;
				$params[] = $category;
			}

			if ($sql) {
				$sql = "REPLACE INTO link_client_to_propertyCategory(clientId, categoryId) VALUES " . implode(',', $sql);
				Yii::app()->db->createCommand($sql)->execute($params);
			}

			$this->propertyCategories = $this->getRelated('propertyCategories', true);
		};

		$this->attachEventHandler('onAfterSave', $callback);
	}

	/**
	 * @return bool|string
	 */
	public function getEmail()
	{
		return filter_var($this->cli_email, FILTER_VALIDATE_EMAIL) ? $this->cli_email : false;
	}

	public function getPrimaryPhoneNumber()
	{
		if ($this->telephones) {
			return $this->telephones[0]->tel_number;
		} else {
			return null;
		}
	}
}
