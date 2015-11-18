<?php

/**
 * This is the model class for table "deal".
 *
 * The followings are the available columns in table 'deal':
 * @property integer                       $dea_id
 * @property string                        $dea_status
 * @property string                        $dea_created
 * @property string                        $dea_launchdate
 * @property string                        $dea_exchdate
 * @property string                        $dea_compdate
 * @property string                        $dea_type
 * @property integer                       $dea_prop
 * @property integer                       $dea_branch
 * @property integer                       $dea_neg
 * @property integer                       $dea_applicant
 * @property integer                       $dea_vendor
 * @property integer                       $dea_solicitor
 * @property integer                       $dea_lender
 * @property string                        $dea_valueprice
 * @property string                        $dea_valuepricemax
 * @property string                        $dea_marketprice
 * @property string                        $dea_tenure
 * @property float                         $dea_commission
 * @property string                        $dea_commissiontype
 * @property string                        $dea_qualifier
 * @property string                        $dea_share
 * @property string                        $dea_otheragent
 * @property string                        $dea_chainfree
 * @property string                        $dea_managed
 * @property string                        $dea_term
 * @property string                        $dea_conditions
 * @property string                        $dea_notes
 * @property integer                       $dea_ptype
 * @property integer                       $dea_psubtype
 * @property string                        $dea_built
 * @property string                        $dea_refurbed
 * @property integer                       $dea_floors
 * @property string                        $dea_floor
 * @property string                        $dea_listed
 * @property integer                       $dea_reception
 * @property integer                       $dea_bedroom
 * @property integer                       $dea_bathroom
 * @property string                        $dea_leaseend
 * @property string                        $dea_strapline
 * @property string                        $dea_description
 * @property string                        $dea_available
 * @property string                        $dea_servicecharge
 * @property string                        $dea_groundrent
 * @property string                        $dea_othercharge
 * @property string                        $dea_key
 * @property string                        $dea_board
 * @property string                        $dea_boardtype
 * @property string                        $dea_keywords
 * @property integer                       $dea_oldid
 * @property integer                       $dea_hits
 * @property string                        $dea_hip
 * @property string                        $dea_contract
 * @property string                        $dea_featured
 * @property integer                       $createdBy
 * @property integer                       $modifiedBy
 * @property integer                       $valuationLetterSent
 * @property string                        $followUpDue
 * @property integer                       $vendorFollowUp
 * @property integer                       $instructionLetterSent
 * @property integer                       $displayOnWebsite
 * @property string                        $feed_line1
 * @property string                        $feed_line2
 * @property string                        $feed_line3
 * @property string                        $feed_line4
 * @property string                        $feed_city
 * @property string                        $emailLinkString
 * @property int                           $noNewProperty
 * @property String                        $title
 *
 *
 * Defined Relations:
 * @property Property                      $property
 * @property Client[]                      $owner
 * @property Feature[]                     $features
 * @property Media[]                       $photos
 * @property Media[]                       $floorplans
 * @property Media                         $epc
 * @property Branch                        $branch
 * @property PropertyType                  $propertyType
 * @property PropertyType                  $propertySubtype
 * @property InstructionVideo[]            $video
 * @property Appointment[]                 $appointments
 * @property User                          $negotiator
 * @property Appointment                   $followUpAppointment
 * @property Mailshot                      $mailshots
 * @property int                           $siteViews
 * @property Appointment[]                 $viewings
 * @property Appointment[]                 $viewingsOnly these are viewings only; without their related clients nor negotiators
 * @property Address                       $address
 * @property MandrillMailshot[]            $mandrillMailshots
 * @property ClientInterest[]              $interest
 * @property PropertyCategory[]            $propertyCategories
 *
 *
 * Defined Scopes:
 * @method Deal available()
 * @method Deal notUnderTheRadar()
 * @method Deal publicAvailable()
 * @method Deal missedFollowUp()
 */
class Deal extends CActiveRecord
{
	const MAILSHOT_TYPE_NEW     = 'new';
	const MAILSHOT_TYPE_REDUCED = 'reduced';
	const MAILSHOT_TYPE_BACK    = 'back';

	/**
	 * @var string
	 */
	const STATUS_VALUATION              = 'Valuation';
	const STATUS_INSTRUCTED             = 'Instructed';
	const STATUS_COMPLETED              = 'Completed';
	const STATUS_AVAILABLE              = 'Available';
	const STATUS_UNDER_OFFER            = 'Under Offer';
	const STATUS_UNDER_OFFER_WITH_OTHER = 'Under Offer with Other';
	const STATUS_PRODUCTION             = 'Production';
	const STATUS_PROOFING               = 'Proofing';
	const STATUS_EXCHANGED              = 'Exchanged';
	const STATUS_COLLAPSED              = 'Collapsed';
	const STATUS_NOT_INSTRUCTED         = 'Not Instructed';
	const STATUS_WITHDRAWN              = 'Withdrawn';
	const STATUS_DISINSTRUCTED          = 'Disinstructed';
	const STATUS_SOLD_BY_OTHER          = 'Sold by Other';
	const STATUS_ARCHIVED               = 'Archived';
	const STATUS_COMPARABLE             = 'Comparable';
	const STATUS_CHAIN                  = 'Chain';
	const STATUS_UNKNOWN                = 'Unknown';

	const TYPE_SALES             = 'Sales';
	const TYPE_LETTINGS          = 'Lettings';
	const VENDOR_FOLLOWED_UP     = 1;
	const VENDOR_NOT_FOLLOWED_UP = 0;
	const QUALIFIER_NONE         = 'None';
	const QUALIFIER_POA          = 'POA';
	const QUALIFIER_OIRO         = 'OIRO';
	const QUALIFIER_OIEO         = 'OIEO';

	const CONFIRMED     = "Confirmed";
	const MESSAGE_LEFT  = "Message Left";
	const NOT_CONFIRMED = "Not Confirmed";

	const DIY_NONE = 'None';
	const DIY_DIY  = 'DIY';
	const DIY_DIT  = 'DIT';

	/**
	 * @var String
	 * This is populated throught appointment relation.
	 */
	public $confirmed;
	/**
	 * @var String
	 * feedback status only populated through appointment -> instructions relation
	 */
	public $feedback;
	/**
	 * @var int
	 * only populated through Instruction TO appointment -> instructions relation
	 */
	public $feedbackId;

	public $ownersNames = '';
	public $dea_status = '';
	public $dea_ptype = '';
	public $dea_type = self::TYPE_SALES;
	public $dea_branch = '';
	public $valuationDate = '';
	public $followUpAppointmentId = 0;
	public $searchString = '';
	public $hitcount;
	/**
	 * Used for search
	 * @var string
	 */
	public $minBedrooms = '';
	public $maxBedrooms = '';

	public $minPrice = '';
	public $maxPrice = '';

	public $matchingPostcodes = [];

	public $followUpUser = '';

	public $valuationPriceMin = '';
	public $valuationPriceMax = '';

	public $DIY = '';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Deal the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	public static function getQualifiers()
	{
		return array_combine($t = [self::QUALIFIER_NONE, self::QUALIFIER_OIEO, self::QUALIFIER_OIRO, self::QUALIFIER_POA], $t);
	}

	/**
	 * @param     $search
	 * @param int $limit
	 * @return static[]
	 */
	public function quickSearch($search, $limit = 5)
	{
		$criteria       = $this->getDbCriteria();
		$criteria->with = ['address'];
		$parts          = explode(' ', $search);
		$criteria->compare('dea_status', $this->publicStatuses());
		$criteria->compare('dea_type', self::TYPE_SALES);

		foreach ($parts as $i => $part) {
			$criteria->addCondition("address.searchString LIKE :part{$i}");
			$criteria->params["part{$i}"] = "%{$part}%";
		}
		if ($limit) {
			$criteria->limit = $limit;
		}
		return $this->findAll($criteria);
	}

	public function launch()
	{

		$this->dea_launchdate = date("Y-m-d H:i:s");
		return $this;
	}

	public function getNextAvailableStatuses()
	{

		$arrayForUnusedStatues = array_combine($t = array(
				self::STATUS_VALUATION,
				self::STATUS_WITHDRAWN,
				self::STATUS_DISINSTRUCTED,
		), $t);

		$nextStatuses = array(
				self::STATUS_VALUATION              => array_combine($t = array(
						self::STATUS_PRODUCTION
				), $t),
				self::STATUS_INSTRUCTED             => array_combine($t = array(
						self::STATUS_PRODUCTION,
						self::STATUS_WITHDRAWN,
						self::STATUS_DISINSTRUCTED,
				), $t),
				self::STATUS_PRODUCTION             => array_combine($t = array(
						self::STATUS_PROOFING,
						self::STATUS_WITHDRAWN,
						self::STATUS_DISINSTRUCTED,
				), $t),
				self::STATUS_PROOFING               => array_combine($t = array(
						self::STATUS_AVAILABLE,
						self::STATUS_PRODUCTION,
						self::STATUS_WITHDRAWN,
						self::STATUS_DISINSTRUCTED,
				), $t),
				self::STATUS_AVAILABLE              => array_combine($t = array(
						self::STATUS_UNDER_OFFER,
						self::STATUS_UNDER_OFFER_WITH_OTHER,
						self::STATUS_WITHDRAWN,
						self::STATUS_DISINSTRUCTED,
				), $t),
				self::STATUS_UNDER_OFFER            => array_combine($t = array(
						self::STATUS_EXCHANGED,
						self::STATUS_COLLAPSED,
						self::STATUS_UNDER_OFFER_WITH_OTHER,
						self::STATUS_WITHDRAWN,
						self::STATUS_DISINSTRUCTED,
				), $t),
				self::STATUS_EXCHANGED              => array_combine($t = array(
						self::STATUS_COMPLETED,
						self::STATUS_COLLAPSED,
						self::STATUS_WITHDRAWN,
						self::STATUS_DISINSTRUCTED,
				), $t),
				self::STATUS_COLLAPSED              => array_combine($t = array(
						self::STATUS_VALUATION,
						self::STATUS_PRODUCTION,
						self::STATUS_PRODUCTION,
						self::STATUS_UNDER_OFFER,
						self::STATUS_WITHDRAWN,
						self::STATUS_DISINSTRUCTED,
				), $t),
				self::STATUS_COMPLETED              => array_combine($t = array(), $t),
				self::STATUS_SOLD_BY_OTHER          => array_combine($t = array(), $t),
				self::STATUS_WITHDRAWN              => array_combine($t = array(
						self::STATUS_PRODUCTION,
						self::STATUS_PROOFING,
				), $t),
				self::STATUS_DISINSTRUCTED          => array_combine($t = array(
						self::STATUS_PRODUCTION,
						self::STATUS_PROOFING,
				), $t),
				self::STATUS_UNDER_OFFER_WITH_OTHER => array_combine($t = array(
						self::STATUS_SOLD_BY_OTHER,
						self::STATUS_AVAILABLE,
				), $t),
				self::STATUS_NOT_INSTRUCTED         => array_combine($t = [self::STATUS_VALUATION], $t),
				self::STATUS_ARCHIVED               => $arrayForUnusedStatues,
				self::STATUS_COMPARABLE             => $arrayForUnusedStatues,
				self::STATUS_CHAIN                  => $arrayForUnusedStatues,
				self::STATUS_UNKNOWN                => $arrayForUnusedStatues,
		);

		if (!$this->dea_status) {
			throw new CException('cannot find next possible statuses for instruction with no status');
		}

		return $nextStatuses[$this->dea_status];

	}

	/**
	 * makes a full copy of an instruction
	 *
	 * @param string $type
	 * @param string $status
	 * @return bool|\Deal
	 * @throws InvalidArgumentException
	 */
	public function copyAs($type = self::TYPE_SALES, $status = self::STATUS_PRODUCTION)
	{

		if (!in_array($type, [self::TYPE_LETTINGS, self::TYPE_SALES])) {
			throw new InvalidArgumentException('type must be one of the following [' . self::TYPE_LETTINGS . ', ' . self::TYPE_SALES . '] actual value is: ' . $type);
		}
		if (!in_array($status, self::getStatuses())) {
			throw new InvalidArgumentException('Status must be in the list [' . implode(', ', self::getStatuses()) . '] actual value is: ' . $status);
		}

		/** @var $property Property */
		$property = Property::model()->findByPk($this->dea_prop);

		$instruction = new Deal('copy');
		$instruction->setAttributes($this->attributes);
		$instruction->dea_type        = $type;
		$instruction->dea_status      = $status;
		$instruction->dea_marketprice = 0;
		$instruction->dea_launchdate  = null;
		$instruction->importFromProperty($property);

		if ($instruction->save(false)) {
			InstructionVideo::copyRecords($this->dea_id, $instruction->dea_id);
			LinkInstructionToFeature::copyRecords($this->dea_id, $instruction->dea_id);
			LinkClientToInstruction::copyRecords($this->dea_id, $instruction->dea_id);
			Media::copyRecords($this->dea_id, $instruction->dea_id);
			return $instruction;
		}
		return false;

	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'deal';
	}

	public function stripHtml($attribute, $options)
	{

		include_once 'Zend/Filter/StripTags.php';
		$allowedAttributes = (isset($options['allowedAttributes']) ? $options['allowedAttributes'] : []);
		$allowedTags       = (isset($options['allowedTags']) ? $options['allowedTags'] : []);
		$strip             = new Zend_Filter_StripTags($allowedTags, $allowedAttributes);
		$this->$attribute  = $strip->filter($this->$attribute);
		return true;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
				[
						'dea_description',
						'stripHtml',
						'allowedTags'       => ['p', 'b', 'i', 'ul', 'li', 'ol', 'a', 'strong', 'em', 'img'],
						'allowedAttributes' => ['class', 'href', 'src']
				],
				['feed_line1, feed_line2,feed_line3,feed_line4,feed_city, title', 'type', 'type' => 'string'],
				array(
						'dea_prop, dea_branch, dea_neg, dea_applicant, dea_vendor, dea_solicitor, dea_lender, dea_ptype, dea_psubtype, dea_floors, dea_reception, dea_bedroom, dea_bathroom, dea_oldid, dea_hits, noPortalFeed, underTheRadar, displayOnWebsite, noNewProperty, createdBy, modifiedBy, followUpUser',
						'numerical',
						'integerOnly' => true
				),
				array(
						'followUpDue, valuationDate, dea_exchdate, dea_compdate',
						'date',
						'allowEmpty' => true,
						'format'     => [
								'dd/MM/yyyy',
								'yyyy-MM-dd'
						]
				),
				['valuationLetterSent,vendorFollowUp,instructionLetterSent', 'in', 'range' => [0, 1]],
				['dea_commission', 'numerical'],
				['dea_status', 'length', 'max' => 22],
				['dea_type, dea_contract', 'length', 'max' => 8],
				['dea_tenure', 'length', 'max' => 17],
				['dea_commissiontype, dea_share', 'length', 'max' => 5],
				['dea_qualifier', 'length', 'max' => 4],
				['dea_otheragent, dea_servicecharge, dea_groundrent, dea_othercharge, dea_key', 'length', 'max' => 100],
				['dea_chainfree, dea_managed, dea_featured', 'length', 'max' => 3],
				['dea_term', 'length', 'max' => 18],
				['dea_built, dea_refurbed, dea_leaseend', 'length', 'max' => 50],
				['dea_floor, dea_hip', 'length', 'max' => 12],
				['dea_listed', 'length', 'max' => 9],
				['dea_strapline', 'length', 'max' => 255],
				['dea_board', 'length', 'max' => 16],
				['dea_boardtype', 'length', 'max' => 11],
				[
						'dea_conditions, dea_notes, dea_description, dea_keywords, dea_hits, dea_hip, dea_contract',
						'length',
						'min' => 1
				],
				['dea_valueprice, dea_valuepricemax, dea_marketprice', 'numerical'],
				['minPrice, maxPrice, minBedrooms,maxBedrooms, valuationPriceMin, valuationPriceMax', 'numerical'],
				['DIY', 'in', 'range' => [self::DIY_NONE, self::DIY_DIY, self::DIY_DIT]],
				array(
						'dea_id, dea_status, dea_created, dea_launchdate, dea_exchdate,
						dea_compdate, dea_type, dea_prop, dea_branch, dea_neg, dea_applicant,
						 dea_vendor, dea_solicitor, dea_lender, dea_valueprice, dea_valuepricemax,
						 dea_marketprice, dea_tenure, dea_commission, dea_commissiontype,
						 dea_qualifier, dea_share, dea_otheragent, dea_chainfree, dea_managed,
						 dea_term, dea_conditions, dea_notes, dea_ptype, dea_psubtype, dea_built,
						 dea_refurbed, dea_floors, dea_floor, dea_listed, dea_reception,
						 dea_bedroom, dea_bathroom, dea_leaseend, dea_strapline, dea_description,
						 dea_available, dea_servicecharge, dea_groundrent, dea_othercharge, dea_key,
						 dea_board, dea_boardtype, dea_keywords, dea_oldid, dea_hits, dea_hip,
						 dea_contract, dea_featured, noPortalFeed, underTheRadar, noNewProperty,emailLinkString,
						 displayOnWebsite, createdBy, modifiedBy,valuationDate',
						'safe',
						'on' => 'search'
				),
				array(
						'dea_status, dea_created, dea_launchdate, dea_exchdate,
						dea_compdate, dea_type, dea_prop, dea_branch, dea_neg, dea_applicant,
						 dea_vendor, dea_solicitor, dea_lender, dea_valueprice, dea_valuepricemax,
						 dea_marketprice, dea_tenure, dea_commission, dea_commissiontype,
						 dea_qualifier, dea_share, dea_otheragent, dea_chainfree, dea_managed,
						 dea_term, dea_conditions, dea_notes, dea_ptype, dea_psubtype, dea_built,
						 dea_refurbed, dea_floors, dea_floor, dea_listed, dea_reception,
						 dea_bedroom, dea_bathroom, dea_leaseend, dea_strapline, dea_description,
						 dea_available, dea_servicecharge, dea_groundrent, dea_othercharge, dea_key,
						 dea_board, dea_boardtype, dea_keywords, dea_oldid, dea_hits, dea_hip,
						 dea_contract, dea_featured, noPortalFeed, underTheRadar, noNewProperty,emailLinkString,
						 displayOnWebsite, createdBy, modifiedBy,valuationDate',
						'safe',
						'on' => 'copy'
				),
		);
	}

	/**
	 * All one line relations should go on top
	 * Multiline relations should use Array long syntax(Array()) one liners — short syntax ([])
	 *
	 * one to one (HAS_ONE and BELONGS_TO) should probably have together=>true
	 *
	 * @return array relational rules.
	 */
	public function relations()
	{

		/**
		 * All one line relations should go on top
		 * Multiline relations should use Array long syntax(Array()) one liners — short syntax ([])
		 */
		return array(
				"epc"                 => [self::HAS_ONE, "Media", "med_row", 'on' => "epc.med_type = 'EPC'", 'order' => 'epc.med_order ASC'],
				"floorplans"          => [self::HAS_MANY, "Media", "med_row", 'on' => "floorplans.med_type = 'Floorplan'", 'order' => 'floorplans.med_order ASC'],
				"photos"              => [self::HAS_MANY, "Media", "med_row", 'on' => "photos.med_type = 'Photograph'", 'order' => 'photos.med_order ASC'],
				"property"            => [self::HAS_ONE, "Property", ["pro_id" => 'dea_prop']],
				'address'             => [self::HAS_ONE, 'Address', ['addressId' => 'id'], 'through' => 'property', 'together' => true],
				"epcCount"            => [self::STAT, "Media", "med_row", 'select' => "count(*)", 'condition' => "med_type = 'EPC'", 'group' => 'med_row'],
				'owner'               => [self::MANY_MANY, 'Client', 'link_client_to_instruction(dealId,clientId)', 'on' => 'owner_owner.capacity = "Owner"', 'together' => true,],
				'features'            => [self::MANY_MANY, 'Feature', 'link_instruction_to_feature(dealId, featureId)'],
				'branch'              => [self::BELONGS_TO, 'Branch', 'dea_branch'],
				'propertyType'        => [self::BELONGS_TO, 'PropertyType', 'dea_ptype', 'together' => true],
				'propertySubtype'     => [self::BELONGS_TO, 'PropertyType', 'dea_psubtype', 'together' => true],
				'video'               => [self::HAS_MANY, 'InstructionVideo', 'InstructionId'],
				'dealSOT'             => [self::HAS_MANY, "StateOfTrade", "sot_deal", 'order' => 'dealSOT.sot_date DESC', 'with' => ['creator' => ['together' => true]],],
				"mailshots"           => [self::HAS_MANY, "Mailshot", "mai_deal", 'order' => 'mailshots.mai_date DESC'],
				'negotiator'          => [self::BELONGS_TO, 'User', 'dea_neg', 'together' => true],
				'followUpAppointment' => [self::BELONGS_TO, 'Appointment', 'followUpAppointmentId', 'together' => true],
				'siteViews'           => [self::STAT, 'PropertyView', 'dea_id'],
				'propertyCategories'  => [self::MANY_MANY, 'PropertyCategory', 'link_instruction_to_propertyCategory(instructionId,categoryId)'],
				'interest' => [self::HAS_MANY, 'ClientInterest', 'instructionId'],
				'tenant'              => array(
						self::MANY_MANY,
						'Client',
						'link_client_to_instruction(dealId,clientId)',
						'on'       => 'tenant_tenant.capacity = "Tenant"',
						'together' => true,
				),
				'mandrillMailshots'   => array(
						self::HAS_MANY,
						'MandrillMailshot',
						'instructionId',
						'with' => ['creator', 'hits', 'hitCount', 'uniqueHitCount', 'emailCount', 'queuedEmailCount', 'sentEmailCount', 'openEmailCount']
				),
				'appointments'        => array(
						self::MANY_MANY,
						'Appointment',
						'link_deal_to_appointment(d2a_dea, d2a_app)',
						'select' => array(
								'*',
								'appointments_appointments.d2a_feedback as appointmentFeedback',
								'appointments_appointments.d2a_id as appointmentFeedbackId',
								'appointments_appointments.d2a_feedback as feedback',
								'appointments_appointments.d2a_id as feedbackId',
						),
						'with'   => ['user' => ['together' => true], 'clients' => ['together' => true]],
						'order'  => 'appointments.app_start DESC'
				),
				'viewings'            => array( // more specific relation with just viewings
												self::MANY_MANY,
												'Appointment',
												'link_deal_to_appointment(d2a_dea, d2a_app)',
												'select' => array(
														'*',
														'viewings_viewings.d2a_feedback as appointmentFeedback',
														'viewings_viewings.d2a_id as appointmentFeedbackId',
														'viewings_viewings.d2a_feedback as feedback',
														'viewings_viewings.d2a_id as feedbackId',

												),
												'on'     => 'viewings.app_type = "' . Appointment::TYPE_VIEWING . '"',
												'with'   => ['user' => ['together' => true], 'clients' => ['together' => true]],
												'order'  => 'viewings.app_start DESC'
				),
				'generalNotes'        => array(
						self::HAS_MANY,
						"Note",
						"not_row",
						'on'    => "generalNotes.not_type = 'deal_general'",
						'order' => 'generalNotes.not_status ASC, generalNotes.not_id DESC'
				),

		);
	}

	/**
	 * @return array
	 */
	public function scopes()
	{

		return array(
				"publicAvailable"  => ['condition' => "dea_status IN ('" . implode("', '", $this->publicStatuses()) . "') OR displayOnWebsite = 1"],
				'available'        => ['condition' => "dea_status = '" . self::STATUS_AVAILABLE . "'"],
				'notUnderTheRadar' => ['condition' => "underTheRadar <> 1"],
				'missedFollowUp'   => array(
						'condition' => 'followUpDue <= :missewdFollowUp_followUpDue AND vendorFollowUp = :missedFollowUp_vendorFollowUp',
						'params'    => array(
								'missewdFollowUp_followUpDue'   => date('Y-m-d'),
								'missedFollowUp_vendorFollowUp' => self::VENDOR_NOT_FOLLOWED_UP
						)
				),
		);
	}

	/**
	 * @param int $count
	 * @return Deal
	 */
	public function epcCount($count = 0)
	{
		$this->getDbCriteria()->mergeWith(array(
												  'select' => '(select COUNT(med_id) FROM ' . Media::model()
																								   ->tableName() . " WHERE med_row = t.dea_id AND med_type = 'EPC' group by t.dea_id) AS epcCount",
												  'having' => 'epcCount = 0 OR epcCount is NULL',
												  'limit'  => 1
										  ));
		return $this;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
				'dea_id'                => 'deal ID',
				'dea_status'            => 'Status',
				'dea_created'           => 'Created',
				'dea_launchdate'        => 'Launchdate',
				'dea_exchdate'          => 'Exchange Date',
				'dea_compdate'          => 'Completion Date',
				'dea_type'              => 'Type',
				'dea_prop'              => 'Prop',
				'dea_branch'            => 'Branch',
				'dea_neg'               => 'Negotiator',
				'dea_applicant'         => 'Applicant',
				'dea_vendor'            => 'Vendor',
				'dea_solicitor'         => 'Solicitor',
				'dea_lender'            => 'Lender',
				'dea_valueprice'        => 'Valuation Price',
				'dea_valuepricemax'     => 'Maximum Valuation Price',
				'dea_marketprice'       => 'Market Price',
				'dea_tenure'            => 'Tenure',
				'dea_commission'        => 'Commission',
				'dea_commissiontype'    => 'Commission Type',
				'dea_qualifier'         => 'Qualifier',
				'dea_share'             => 'Deal Share',
				'dea_otheragent'        => 'Otheragent',
				'dea_chainfree'         => 'Chainfree',
				'dea_managed'           => 'Managed',
				'dea_term'              => 'Term',
				'dea_conditions'        => 'Conditions',
				'dea_notes'             => 'Notes',
				'dea_ptype'             => 'Property Type',
				'dea_psubtype'          => 'Property subtype',
				'dea_built'             => 'Built',
				'dea_refurbed'          => 'Refurbed',
				'dea_floors'            => 'Floor(s)',
				'dea_floor'             => 'Floor',
				'dea_listed'            => 'Listed',
				'dea_reception'         => 'Reception(s)',
				'dea_bedroom'           => 'Bedroom(s)',
				'dea_bathroom'          => 'Bathroom(s)',
				'dea_leaseend'          => 'Lease Expires',
				'dea_strapline'         => 'Strapline',
				'dea_description'       => 'Description',
				'dea_available'         => 'Available',
				'dea_servicecharge'     => 'Service Charge',
				'dea_groundrent'        => 'Ground Rent',
				'dea_othercharge'       => 'Other Charge',
				'dea_key'               => 'Key',
				'dea_board'             => 'Board',
				'dea_boardtype'         => 'Board Type',
				'dea_keywords'          => 'Keywords',
				'dea_oldid'             => 'Oldid',
				'dea_hits'              => 'Hits',
				'dea_hip'               => 'HIP Status',
				'dea_contract'          => 'Contract',
				'dea_featured'          => 'Featured',
				'valuationDate'         => 'Valuation Date',
				'minBedrooms'           => 'Beds',
				'maxBedrooms'           => 'Beds',
				'minPrice'              => 'Price',
				'maxPrice'              => 'Price',
				'noPortalFeed'          => 'No portal Feed',
				'underTheRadar'         => 'Under the Radar',
				'displayOnWebsite'      => 'Display on Website',
				'valuationLetterSent'   => 'Valuation Letter Sent',
				'vendorFollowUp'        => 'Vendor Follow Up',
				'instructionLetterSent' => 'Instruction Letter Sent',
				'followUpDue'           => 'Follow Up Due',
				'createdBy'             => 'Created By',
				'modifiedBy'            => 'Modified By',
				'followUpAppointmentId' => 'Follow Up Appointment\'s Id',
				'noNewProperty'         => 'Not as a New Property',
				'emailLinkString'       => 'Email Random String',
				'title'                 => 'Title',
				'feed_line1'            => 'Feed Line 1',
				'feed_line2'            => 'Feed Line 2',
				'feed_line3'            => 'Feed Line 3',
				'feed_line4'            => 'Feed Line 4',
				'feed_city' => 'Feed City',
				'DIY'       => 'DIY'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @param CDbCriteria $criteria
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(CDbCriteria $criteria = null)
	{

		$this->getDbCriteria()->mergeWith($criteria ? $criteria : array());

		$this->dea_neg    = array_filter((array)$this->dea_neg);
		$this->dea_status = array_filter((array)$this->dea_status);

		$criteria           = $this->getDbCriteria();
		$criteria->with     = CMap::mergeArray($criteria->with, ['owner', 'negotiator', 'address']);
		$criteria->together = true;

		$criteria->compare('dea_bedroom', '>=' . $this->minBedrooms);
		$criteria->compare('dea_bedroom', '<=' . $this->maxBedrooms);
		$criteria->compare('dea_marketprice', '>=' . $this->minPrice);
		$criteria->compare('dea_marketprice', '<=' . $this->maxPrice);
		$criteria->compare('dea_valueprice', '>=' . $this->valuationPriceMin);
		$criteria->compare('dea_valueprice', '<=' . $this->valuationPriceMax);
		$criteria->compare('DIY', $this->DIY);

		if ($this->dea_psubtype) {
			$psubtypes = (array)$this->dea_psubtype;
			$ptypes    = $this->dea_ptype ? "dea_ptype in (" . implode(",", (array)$this->dea_ptype) . ") OR " : "";
			$criteria->addCondition($ptypes . "dea_psubtype in (" . implode(",", $psubtypes) . ")");
		} else {
			$criteria->compare('dea_ptype', $this->dea_ptype);
		}
		$criteria->compare('dea_branch', $this->dea_branch);

		$criteria->addInCondition('dea_type', (array)$this->dea_type);
		if ($this->dea_status) {
			$criteria->addInCondition('dea_status', (array)$this->dea_status);
		} else {
			$criteria->addCondition('1 = 0'); // if no status provided; empty result should be returned 1=0 will say mysql not to return anything
		}

		if ($this->dea_neg) {
			$criteria->addInCondition('dea_neg', (array)$this->dea_neg);
		}

		if ($this->searchString) {
			$parts = explode(' ', trim($this->searchString));
			foreach ($parts as $key => $part) {
				$criteria->addCondition("(owner.cli_fname LIKE :part" . $key . " OR owner.cli_sname LIKE :part" . $key . " OR concat_ws(' ', address.line1,address.line2,address.line3,address.line4,address.line5,address.postcode) LIKE :part" . $key . ")");
				$criteria->params['part' . $key] = '%' . $part . '%';
			}
		}

		if ($this->matchingPostcodes) {
			$postcodeCondition = "";
			$cnt               = 0;
			foreach ($this->matchingPostcodes as $postcodeFirstPart) {
				$cnt++;
				$operator          = $cnt == count($this->matchingPostcodes) ? "" : " OR ";
				$postcodeCondition = $postcodeCondition . "address.postcode LIKE '" . $postcodeFirstPart . "%'" . $operator;
			}
			$criteria->addCondition($postcodeCondition);
		}

		return $this->createDataProvider($criteria);
	}

	protected function beforeValidate()
	{

		$this->dea_marketprice   = preg_replace('/[^0-9.]/', '', $this->dea_marketprice);
		$this->dea_valuepricemax = preg_replace('/[^0-9.]/', '', $this->dea_valuepricemax);
		$this->dea_valueprice    = preg_replace('/[^0-9.]/', '', $this->dea_valueprice);
		return parent::beforeValidate();
	}

	public function searchMissedFollowUp()
	{

		$criteria                         = new CDbCriteria();
		$criteria->scopes                 = 'missedFollowUp';
		$criteria->with['branch']         = ['together' => true];
		$criteria->with[]                 = 'propertyType';
		$this->dea_status                 = self::STATUS_VALUATION;
		$dataProvider                     = $this->search($criteria);
		$dataProvider->sort->defaultOrder = "followUpDue ASC"; // change sort order for missed follow ups; oldest on top as more urgent
		$dataProvider->sort->attributes   = array(

				'address.postcode'          => array(
						'asc'  => 'address.postcode ASC',
						'desc' => 'address.postcode DESC'
				),
				'negotiator.use_fname'      => array(
						'asc'  => 'negotiator.use_fname ASC',
						'desc' => 'negotiator.use_fname DESC'
				),
				'address.fullAddressString' => array(
						'asc'  => "address.searchString ASC",
						'desc' => "address.searchString DESC",
				),
				'owners'                    => array(
						'asc'  => "CONCAT(owner.cli_fname, owner.cli_sname) ASC",
						'desc' => "CONCAT(owner.cli_fname, owner.cli_sname) DESC",
				),
				'branch.bra_title'          => array(
						'asc'  => "branch.bra_title ASC",
						'desc' => "branch.bra_title DESC",
				),
				'propertyType.pty_title'    => array(
						'asc'  => "propertyType.pty_title ASC",
						'desc' => "propertyType.pty_title DESC",
				),
				'*',
		);
		return $dataProvider;
	}

	private function createDataProvider(CDbCriteria $criteria, $params = array())
	{

		return new CActiveDataProvider($this, CMap::mergeArray($params, Array(
				'criteria' => $criteria,
				'sort'     => array(
						'defaultOrder' => 'dea_created DESC',
						'attributes'   => array(

								'address.postcode'          => array(
										'asc'  => 'address.postcode ASC',
										'desc' => 'address.postcode DESC'
								),
								'negotiator.use_fname'      => array(
										'asc'  => 'negotiator.use_fname ASC',
										'desc' => 'negotiator.use_fname DESC'
								),
								'address.fullAddressString' => array(
										'asc'  => "address.searchString ASC",
										'desc' => "address.searchString DESC",
								),
								'owners'                    => array(
										'asc'  => "CONCAT(owner.cli_fname, owner.cli_sname) ASC",
										'desc' => "CONCAT(owner.cli_fname, owner.cli_sname) DESC",
								),
								'*',
						)
				),
		), Yii::app()->params['CActiveDataProvider']));
	}

	/**
	 *
	 * @param Client $client
	 * @return CActiveDataProvider
	 */
	public function findMathingPropertyByClient(Client $client)
	{

		$criteria = new CDbCriteria();
		$areas    = explode('|', $client->cli_area); // postcodes
		/** @var $propertyAreas PropertyArea */
		$propertyAreas = PropertyArea::model()->findAllByAttributes(array('are_postcode' => $areas));
		$areas         = [];
		foreach ($propertyAreas as $value) {
			$areas[] = $value->are_id;
		}
		$criteria->together = true;
		$criteria->with     = array('property', 'propertyType', 'propertySubtype');
		$criteria->addInCondition('property.pro_area', $areas);
		$criteria->addBetweenCondition('dea_marketprice', $client->cli_salemin, $client->cli_salemax);
		$criteria->compare('dea_bedroom', '>=' . $client->cli_salebed);
		$criteria->compare('dea_type', 'sales');

		$criteria->addInCondition('dea_ptype', explode("|", $client->cli_saleptype));
		$criteria->addInCondition('dea_psubtype', explode("|", $client->cli_saleptype), 'OR');
		$criteria->compare('dea_status', 'Available');

		$CActiveDataProvider = new CActiveDataProvider($this, array(
				'criteria'   => $criteria,
				'pagination' => false
		));
		return $CActiveDataProvider;

	}

	/**
	 *
	 * returns
	 * -1 if status has lower position in status list
	 * 0 if equals
	 * 1 if higher
	 *
	 * @param $status
	 * @return mixed
	 */
	public function statusCompare($status)
	{

		return self::compareStatuses($this->dea_status, $status);
	}

	/**
	 * @param CDbCriteria $criteria
	 * @return CActiveDataProvider
	 */
	public function publicSearch(CDbCriteria $criteria = null)
	{

		$criteria = $criteria ? : new CDbCriteria();

		return new CActiveDataProvider($this, ['pagination' => ['pageSize' => 14], 'criteria' => $criteria]);
	}

	/**
	 * @param string $delimeter
	 * @return string
	 */
	public function getOwnersNames($delimeter = ', ')
	{

		$ownersNames = array();
		foreach ($this->owner as $owner) {
			$ownersNames[] = $owner->getFullName();
		}
		return implode($delimeter, $ownersNames);

	}

	/**
	 * @param string $delimeter
	 * @return string
	 */
	public function getTenantNames($delimeter = ', ')
	{

		$tenantsNames = array();
		foreach ($this->tenant as $tenant) {
			$tenantsNames[] = $tenant->fullName;
		}
		return implode($delimeter, $tenantsNames);

	}

	/**
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
	 * @todo make static
	 * @return array
	 */
	public function getStatusesList()
	{

		return self::getStatuses();
	}

	protected function beforeSave()
	{

		if (!$this->isNewRecord) {
			/** @var $oldRecord Deal */
			$oldRecord = $this->findByPk($this->dea_id); // retrieve old version of that record;
			foreach ($this->attributes as $eachAttr => $eachAttrVal) {
				if ($oldRecord->$eachAttr !== $this->$eachAttr) {
					$change              = new Changelog();
					$change->cha_table   = $this->tableName();
					$change->cha_old     = $oldRecord->$eachAttr;
					$change->cha_new     = $this->$eachAttr;
					$change->cha_field   = $eachAttr;
					$change->cha_row     = $this->dea_id;
					$change->cha_session = session_id();
					$change->cha_action  = Changelog::ACTION_UPDATE;
					$change->save();
				}
			}
			if ($oldRecord->dea_status !== $this->dea_status) {
				$stateOfTrade             = new StateOfTrade();
				$stateOfTrade->sot_deal   = $this->dea_id;
				$stateOfTrade->sot_status = $this->dea_status;
				$stateOfTrade->save(false);

				if ($this->dea_status == self::STATUS_AVAILABLE) {
					$this->launch();
				}
			}

		} else {
			$this->dea_created = date('Y-m-d H:i:s');
			$this->createdBy   = Yii::app()->user->id;
		}

		$this->followUpDue     = Date::parseDate($this->followUpDue);
		$this->valuationDate   = Date::parseDate($this->valuationDate);
		$this->dea_exchdate    = Date::parseDate($this->dea_exchdate);
		$this->dea_compdate    = Date::parseDate($this->dea_compdate);
		$this->modifiedBy      = Yii::app()->user->id;
		$this->dea_marketprice = (float)preg_replace('/[^0-9.]/i', '', $this->dea_marketprice);

		return parent::beforeSave();
	}

	/**
	 * @return float|int
	 */
	public function getInternalArea()
	{

		$internalArea = 0;
		if ($this->floorplans) {
			foreach ($this->floorplans as $floorplan) {
				$internalArea += (float)$floorplan->med_dims;
			}
		}
		return $internalArea;
	}

	/**
	 * @param $type
	 * @return float|string
	 */
	public function getPrice($type = '')
	{

		if ($type == 'pcm') {
			return ceil(($this->dea_marketprice * 52) / 12);
		} else {
			return $this->dea_marketprice;
		}
	}

	/**
	 * @return string
	 */
	public function getQualifier()
	{

		if (strtolower($this->dea_qualifier) == 'none') {
			return '';
		} else {
			return strtoupper($this->dea_qualifier);
		}
	}

	/**
	 * @return string
	 */
	public function getQualifierText()
	{

		if (strtolower($this->dea_qualifier) == 'none') {
			return '';
		} else {
			return strtoupper($this->dea_qualifier);
		}
	}

	/**
	 * @param $start
	 * @param $finish
	 * @return array
	 */
	public function getTotalChoices($start, $finish)
	{

		$result = array();
		for ($i = $start; $i <= $finish; $i++) {
			$result[$i] = $i;
		}
		return $result;
	}

	/**
	 * @todo make static
	 * @return array
	 */
	public function getFloorTypes()
	{

		return array(
				'NA'           => 'NA',
				'Lower Ground' => 'Lower Ground',
				'Ground'       => 'Ground',
				'First'        => 'First',
				'Second'       => 'Second',
				'Third'        => 'Third',
				'Fourth'       => 'Fourth',
				'Fifth'        => 'Fifth',
				'Sixth'        => 'Sixth',
				'Seventh'      => 'Seventh',
				'Eighth'       => 'Eighth',
				'Ninth'        => 'Ninth',
				'Tenth'        => 'Tenth',
				'Eleventh'     => 'Eleventh',
				'Twelfth'      => 'Twelfth',
				'Thirteenth'   => 'Thirteenth',
				'Fourteenth'   => 'Fourteenth',
				'Fifteenth'    => 'Fifteenth',
				'Sixteenth'    => 'Sixteenth',
				'Seventeenth'  => 'Seventeenth',
				'Eighteenth'   => 'Eighteenth',
				'Nineteenth'   => 'Nineteenth',
				'Twentieth'    => 'Twentieth'
		);
	}

	/**
	 * @param $featureId
	 * @return bool
	 */
	public function dealBelongsToFeature($featureId)
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
	 * since is not being used, as generally pointless argument in this methods logic
	 *
	 * @param        $num
	 * @param        $since @deprecated
	 * @param string $type
	 * @return Deal[]
	 */
	public function getMostViewed($num, $since, $type = self::TYPE_SALES)
	{

		$criteria = new CDbCriteria();
		$criteria->compare('dea_type', $type);
		$criteria->scopes = array('available', 'notUnderTheRadar');
		$criteria->limit  = $num;
		$criteria->with   = array('property');
		$criteria->select = array('t.*', 'count(t2.id) as hitcount');
		$criteria->join   = "LEFT JOIN propertyviews t2 on t.dea_id = t2.dea_id";
//		$criteria->addCondition("t2.datetime >= '" . $since . "' AND t2.datetime IS NOT NULL AND t2.datetime <> ''", "AND");
		$criteria->group = 't2.dea_id';
		$criteria->order = "hitcount DESC";
		return $this->findAll($criteria);

	}

	public function getCategorized($categoryId, $type = 'sales', $limit)
	{

		if (!$categoryId) {
			return false;
		}
		$criteria = new CDbCriteria();
		if ($limit) {
			$criteria->limit = $limit;
		}
		if ($type) {
			$criteria->compare('dea_type', $type);
		}

		$instructionIds = LinkInstructionToPropertyCategory::model()->findAllByAttributes(['categoryId' => $categoryId]);
		$instructions   = [];
		foreach ($instructionIds as $value) {
			$instructions[] = $value->instructionId;
		}
		$criteria->addInCondition('dea_id', $instructions);
		$criteria->scopes = array('publicAvailable');
		$criteria->order  = "dea_launchdate DESC";
		return $this->findAll($criteria);
	}

	/**
	 * @param int    $limit
	 * @param string $type
	 * @internal param $num
	 * @return Deal[]
	 */
	public function getLatest($limit = 5, $type = 'sales')
	{

		$criteria = new CDbCriteria();
		$criteria->compare('dea_type', $type);
		$criteria->scopes    = array('available', 'notUnderTheRadar');
		$criteria->condition = "noNewProperty <> 1";
		$criteria->limit     = $limit;
		$criteria->order     = "dea_launchdate DESC";

		return $this->findAll($criteria);

	}

	/**
	 * returns Media entity representing first image(main) or null if there is no such image
	 *
	 * @return Media|null returns Media entity representing first image(main) or null if there is no such image
	 */
	public function getMainImage()
	{

		if (isset($this->photos[0])) {
			return $this->photos[0];
		}
		return null;

	}

	/**
	 * @param $status1
	 * @param $status2
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public static function compareStatuses($status1, $status2)
	{

		$statuses = array_flip(array(
									   'Valuation',
									   'Instructed',
									   'Production',
									   'Proofing',
									   'Available',
									   'Under Offer',
									   'Exchanged',
									   'Completed',
									   'Collapsed',
									   'Not Instructed',
									   'Withdrawn',
									   'Disinstructed',
									   'Under Offer with Other',
									   'Sold by Other',
							   ));

		if (!isset($statuses[$status2]) || !in_array($status2, $statuses)) {
			throw new InvalidArgumentException('Status: ' . $status2 . ' is not valid status for instruction. possible values are: [' . implode(', ', array_keys($statuses)) . ']
							or [' . implode(', ', $statuses) . '] respectively');
		}

		if (isset($statuses[$status2])) {
			return $statuses[$status1] - $statuses[$status2];
		}
	}

	/**
	 * @return array
	 */
	public static function getMailshotTypes()
	{

		return array(
				self::MAILSHOT_TYPE_NEW     => 'A new property has been added',
				self::MAILSHOT_TYPE_REDUCED => 'A property has been reduced in price',
				self::MAILSHOT_TYPE_BACK    => 'A property has come back on the market',
		);
	}

	public function importFromProperty(Property $property)
	{

		$this->dea_bedroom       = $property->pro_bedroom;
		$this->dea_reception     = $property->pro_reception;
		$this->dea_ptype         = $property->pro_ptype;
		$this->dea_psubtype      = $property->pro_psubtype;
		$this->dea_bathroom      = $property->pro_bathroom;
		$this->dea_floors        = $property->pro_floors;
		$this->dea_floor         = $property->pro_floor;
		$this->dea_built         = $property->pro_built;
		$this->dea_refurbed      = $property->pro_refurbed;
		$this->dea_tenure        = $property->pro_tenure;
		$this->dea_leaseend      = $property->pro_leaseend;
		$this->dea_listed        = $property->pro_listed;
		$this->dea_servicecharge = $property->servicecharge;
		$this->dea_groundrent    = $property->groundrent;
		$owners                  = [];
		$tenants                 = [];
		foreach ($property->owners as $value) {
			$owners[] = $value->cli_id;
		}
		foreach ($property->tenants as $value) {
			$tenants[] = $value->cli_id;
		}

		$this->feed_line1 = $this->feed_line1 ? : $property->address->line1;
		$this->feed_line2 = $this->feed_line2 ? : $property->address->line3;
		$this->feed_line3 = $this->feed_line3 ? : $property->address->line2;
		$this->feed_line4 = $this->feed_line4 ? : $property->address->line4;
		$this->feed_city  = $this->feed_city ? : $property->address->line5;

		$this->title = $property->getShortAddressString();

		if (!$this->isNewRecord) {
			$this->setOwners($owners);
			$this->setTenants($tenants);
		} else {
			$run = false;
			$this->attachEventHandler('onAfterSave', function (CEvent $event) use ($owners, $tenants, &$run) {

				if (!$run) {
					$event->sender->setOwners($owners);
					$event->sender->setTenants($tenants);
					$run = true;
				}

			});
		}

	}

	public function setOwners($owners)
	{

		$this->setClients($owners);
	}

	public function setTenants($tenants)
	{

		$this->setClients($tenants, 'Tenant');

	}

	public function setClients(Array $clients, $capacity = 'Owner')
	{

		if (!in_array($capacity, ['Owner', 'Tenant'])) {
			throw new InvalidArgumentException('$capacity must be in list [Owner, Tenant]; Passed value: ' . $capacity);
		}

		if (!$this->isNewRecord || $this->dea_id) {
			$sql = "DELETE FROM link_client_to_instruction WHERE dealId = ? AND capacity = ?";
			Yii::app()->db->createCommand($sql)->execute([$this->dea_id, $capacity]);

			$sql = [];

			foreach ($clients as $value) {
				$sql[] = "('" . $this->dea_id . "', '" . $value . "', '" . $capacity . "')";
			}

			if ($sql) {
				$sql = "REPLACE INTO link_client_to_instruction (dealId, clientId, capacity) VALUES " . implode(',', $sql) . "";
				Yii::app()->db->createCommand($sql)->execute();
			}
		}

	}

	/**
	 * @param string $separator
	 * @return string
	 */
	public function getPropertyRoomString($separator = " ")
	{

		$bedrooms   = $this->dea_bedroom ? $this->dea_bedroom . " " . ($this->dea_bedroom > 1 ? 'bedrooms' : 'bedroom') : '';
		$receptions = $this->dea_reception ? $this->dea_reception . " " . ($this->dea_reception > 1 ? 'receptions' : 'reception') : '';
		$bathrooms  = $this->dea_bathroom ? $this->dea_bathroom . " " . ($this->dea_bathroom > 1 ? 'bathrooms' : 'bathroom') : '';

		$propertyRoomString = $bedrooms . ($bedrooms ? $separator : "") . $receptions . ($receptions ? $separator : "") . $bathrooms;

		return trim($propertyRoomString);
	}

	/**
	 * @return $this
	 */
	public function inStatus()
	{

		$statuses = func_get_args();
		if ($statuses) {
			$this->getDbCriteria()->mergeWith(['condition' => "dea_status IN ('" . implode("', '", $statuses) . "')",]);
		}
		return $this;
	}

	public function statusInList()
	{

		$statuses = func_get_args();
		$statuses = $statuses ? : [];
		if (isset($statuses[0]) && is_array($statuses[0])) {
			$statuses = $statuses[0];
		}
		return in_array($this->dea_status, $statuses);
	}

	public static function getActiveStatuses()
	{

		return array(
				self::STATUS_PRODUCTION,
				self::STATUS_PROOFING,
				self::STATUS_AVAILABLE,
				self::STATUS_UNDER_OFFER,
				self::STATUS_UNDER_OFFER_WITH_OTHER,
				self::STATUS_VALUATION,
				self::STATUS_EXCHANGED,
		);
	}

	public static function getStatuses()
	{

		return array_combine($t = array(
				self::STATUS_VALUATION,
				self::STATUS_INSTRUCTED,
				self::STATUS_COMPLETED,
				self::STATUS_AVAILABLE,
				self::STATUS_UNDER_OFFER,
				self::STATUS_UNDER_OFFER_WITH_OTHER,
				self::STATUS_PRODUCTION,
				self::STATUS_PROOFING,
				self::STATUS_EXCHANGED,
				self::STATUS_COLLAPSED,
				self::STATUS_NOT_INSTRUCTED,
				self::STATUS_WITHDRAWN,
				self::STATUS_DISINSTRUCTED,
				self::STATUS_SOLD_BY_OTHER,
				self::STATUS_ARCHIVED,
				self::STATUS_COMPARABLE,
				self::STATUS_CHAIN,
				self::STATUS_UNKNOWN,
		), $t);
	}

	protected function afterSave()
	{

		/** @var $user User */

		$userId = $this->followUpUser ? : Yii::app()->user->id;
		$user   = User::model()->findByPk($userId);
		if ($this->followUpDue) {
			if ($note = $this->followUpAppointment) {

				$note->app_start  = date("Y-m-d", strtotime($this->followUpDue));
				$note->app_user   = $user ? $user->use_id : $note->app_user;
				$note->calendarID = $user ? $user->use_branch : $note->calendarID;

			} else {
				if (!$user) {
					throw new Exception('User for follow up is not selected');
				}
				$note              = new Appointment();
				$note->app_type    = Appointment::TYPE_VALUATION_FOLLOW_UP;
				$note->app_start   = date("Y-m-d", strtotime($this->followUpDue));
				$note->app_user    = $user->use_id;
				$note->calendarID  = $user->use_branch;
				$note->app_subject = $this->property->address->toString(', ');
				$note->setInstructions([$this->dea_id]);
			}

			$existingFollowUps = Appointment::model()->findByAttributes(array(
																				"app_user" => $note->app_user,
																				'app_type' => Appointment::TYPE_VALUATION_FOLLOW_UP,

																		), array(
																				'order'     => 'app_start DESC',
																				'condition' => 'DATE(app_start) = "' . date("Y-m-d", strtotime($note->app_start)) . '"',
																		));
			if (!$existingFollowUps) {
				$note->app_start = date("Y-m-d", strtotime($note->app_start)) . " 09:00:00"; // hardcoded time, not best solution ever
				$note->app_end   = date("Y-m-d", strtotime($note->app_start)) . " 09:30:00";
			} else {
				$note->app_start = $existingFollowUps->app_end;
				$note->app_end   = date("Y-m-d H:i:s", strtotime($note->app_start . " + 30 minutes"));
			}

			if ($note->save()) {
				$this->followUpAppointmentId = $note->app_id;
				$this->saveAttributes(['followUpAppointmentId']);
			} else {
				$this->addError("followUpUppointment", "Could not save follow up appointment");
			}
			$this->followUpAppointment = $note;
		}

		parent::afterSave();

	}

	/**
	 * @param $categoryId
	 * @return bool
	 */
	public function instructionBelongsToCategory($categoryId)
	{

		static $categoriesCache;
		if ($categoriesCache === null) {
			$categoriesCache = array();
			foreach ($this->propertyCategories as $category) {
				$categoriesCache[] = $category->id;
			}
		}
		return in_array($categoryId, $categoriesCache);
	}

	public function addMailshot(MandrillMessage $message, MailshotType $type)
	{
		$sql = "INSERT INTO link_instruction_to_mandrillMessage SET instructionId = ?, mandrillMessageId = ?, mailshotType = ?";
		return Yii::app()->db->createCommand($sql)->execute([$this->dea_id, $message->id, $type->name]);
	}

	/**
	 * return true if dea_status in('Available', 'Under Offer' ,'Under Offer With Other', 'Exchanged') OR displayOnWebsite = 1"
	 */
	public function isPublic()
	{
		return in_array($this->dea_status, $this->publicStatuses()) || $this->displayOnWebsite;
	}

	private function publicStatuses()
	{
		return [self::STATUS_AVAILABLE, self::STATUS_UNDER_OFFER, self::STATUS_UNDER_OFFER_WITH_OTHER, self::STATUS_EXCHANGED];
	}

	/**
	 * A scope to find all instructions onflicting with some particular instruction.
	 *
	 * Definition of conflicting instruction is:
	 *
	 * same property id. different instruction id and one of the statuses that indicate that instruction is being processed
	 *
	 * @param Deal $instruction
	 * @return $this
	 */
	public function conflictingWith(Deal $instruction)
	{
		$criteria = $this->getDbCriteria();
		$criteria->compare('dea_prop', $instruction->dea_prop);
		$criteria->compare('dea_id', '<>' . $instruction->dea_id);
		$criteria->compare('dea_status', array(
				self::STATUS_AVAILABLE,
				self::STATUS_UNDER_OFFER,
				self::STATUS_UNDER_OFFER_WITH_OTHER,
				self::STATUS_PRODUCTION,
				self::STATUS_PROOFING,
				self::STATUS_INSTRUCTED
		));
		return $this;
	}

	/**
	 * @param $categories
	 */
	public function setCategories($categories)
	{
		try {
			$sql = "DELETE FROM link_instruction_to_propertyCategory WHERE instructionId = ?";
			Yii::app()->db->createCommand($sql)->execute([$this->dea_id]);

			$sql = $params = [];
			foreach ($categories as $categoryId) {
				$sql[]    = '(?, ?)';
				$params[] = $this->dea_id;
				$params[] = $categoryId;
			}

			if ($sql) {
				$sql = 'REPLACE INTO link_instruction_to_propertyCategory (instructionId, categoryId) VALUES ' . implode(',', $sql);
				Yii::app()->db->createCommand($sql)->execute($params);
			}

			return true;
		} catch (Exception $e) { // should we record?
			return false; // db threw an exception
		}

	}

	public function setFeatures($features)
	{
		try {
			$sql = "DELETE FROM link_instruction_to_feature WHERE dealId = ?";
			Yii::app()->db->createCommand($sql)->execute([$this->dea_id]);

			$sql = $params = [];

			foreach ($features as $feature) {
				$sql[]    = '(?, ?)';
				$params[] = $this->dea_id;
				$params[] = $feature;
			}

			if ($sql) {
				$sql = 'REPLACE INTO link_instruction_to_feature(dealId, featureId) VALUES ' . implode(',', $sql);
				Yii::app()->db->createCommand($sql)->execute($params);
			}
			return true;
		} catch (Exception $e) {
			return false; // db threw an exception
		}
	}

	public function isDIY($type = null)
	{
		if ($type === null) {
			return $this->DIY !== self::DIY_NONE;
		} else {
			return $this->DIY === $type;
		}
	}

	public function registerInterest($clientId, $type = null)
	{

		$sql     = "INSERT INTO clientInterest SET
		instructionId = :dealId
		, clientId = :clientId
		, email = :email
		, `text` = :text
		, created = :created
		, createdBy = :createdBy ON DUPLICATE KEY UPDATE
		 email = IF(email = 1, 1, :email)
		 , `text` = IF(`text` = 1, 1, :text)
		";
		$params  = array(
				'created'   => date('Y-m-d H:i:s'),
				'createdBy' => Yii::app()->user->id,
				'dealId'    => $this->dea_id,
				'clientId'  => $clientId,
				'email'     => $type === 'email' ? 1 : 0,
				'text'      => $type === 'text' ? 1 : 0,
		);
		$command = Yii::app()->db->createCommand($sql);
		return $command->execute($params);
	}

}
