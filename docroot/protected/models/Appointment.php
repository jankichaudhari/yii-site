<?php

/**
 * This is the model class for table "appointment".
 *
 * The followings are the available columns in table 'appointment':
 * @property integer         $app_id
 * @property string          $app_status
 * @property string          $app_type
 * @property integer         $app_subtype
 * @property string          $app_subtypeOLD
 * @property string          $app_notetype
 * @property string          $app_subject
 * @property integer         $app_bookedby
 * @property integer         $app_user
 * @property integer         $calendarID
 * @property string          $app_notes
 * @property string          $app_session
 * @property string          $app_allday
 * @property string          $app_private
 * @property string          $app_created
 * @property string          $app_updated
 * @property User            $user
 * @property User            $bookedBy
 * @property Client[]        $clients
 * @property Branch          $branch
 * @property Address[]       $addresses
 * @property Property        $property
 * @property Deal[]          $instructions
 * @property Sms[]           $sms
 * @property integer         $followUpAppointmentId Recursive link to a follow up viewing
 * @property Appointment     $followUpAppointment   Recursive link to a follow up viewing
 *
 *
 * @method Appointment active()
 * @method Appointment cancelled()
 * @method Appointment deleted()
 * @method Appointment passed()
 */
class Appointment extends CActiveRecord
{

	const ALLDAY_NO        = 'No';
	const ALLDAY_YES       = 'Yes';
	const SCENARIO_COPY    = 'copy';
	const SCENARIO_CONSOLE = 'console';
	public $startTime;
	public $endTime;

	/**
	 * @var String
	 * feedback status only populated through Instruction TO appointment relation DEPRECATED
	 * @deprecated
	 */
	public $appointmentFeedback;
	/**
	 * @var String
	 * feedback status only populated through Instruction TO appointment relation
	 */
	public $feedback;
	/**
	 * @var int
	 * id of link_deal_to_appointment only populated through Instruction TO appointment relation
	 * @deprecated
	 */
	public $appointmentFeedbackId;
	/**
	 * @var int
	 * only populated through Instruction TO appointment relation
	 */
	public $feedbackId;

	public $app_user = '';
	public $app_start = '';
	public $app_end = '';
	public $app_type = '';
	public $app_notetype = '';

	public $searchString = '';

	public $DIT = '';

	const TYPE_VIEWING             = 'Viewing';
	const TYPE_VALUATION           = 'Valuation';
	const TYPE_PRODUCTION          = 'Production';
	const TYPE_INSPECTION          = 'Inspection';
	const TYPE_MEETING             = 'Meeting';
	const TYPE_NOTE                = 'Note';
	const TYPE_LUNCH               = 'Lunch';
	const TYPE_VALUATION_FOLLOW_UP = 'Valuation Follow Up';
	const TYPE_VIEWING_FOLLOW_UP   = 'Viewing Follow Up';

	/**
	 * @deprecated
	 */
	const TYPE_FOLLOW_UP = 'Follow Up';

	const NOTE_TYPE_NOTE     = 'Note';
	const NOTE_TYPE_HOLIDAY  = 'Holiday';
	const NOTE_TYPE_DAY_OFF  = 'Day off';
	const NOTE_TYPE_SICK_DAY = 'Sick day';

	const STATUS_CANCELLED = 'Cancelled';
	const STATUS_ACTIVE    = 'Active';
	const STATUS_DELETED   = 'Deleted';

	const DIT_BOOKED = 1; // 1 it's just booked.
	const DIT_WITH_FOLLOW_UP = 2; // after the app is passed there must be a new note

	public function __construct($scenario = 'insert')
	{

		return parent::__construct($scenario);
	}

	public static function getTypes()
	{

		return array_combine($t = array(
				self::TYPE_VIEWING,
				self::TYPE_VALUATION,
				self::TYPE_PRODUCTION,
				self::TYPE_INSPECTION,
				self::TYPE_MEETING,
				self::TYPE_NOTE,
				self::TYPE_LUNCH,
				self::TYPE_VIEWING_FOLLOW_UP,
				self::TYPE_VALUATION_FOLLOW_UP,
		), $t);
	}

	public static function getNoteTypes()
	{

		return array_combine($t = array(
				self::NOTE_TYPE_NOTE,
				self::NOTE_TYPE_HOLIDAY,
				self::NOTE_TYPE_DAY_OFF,
				self::NOTE_TYPE_SICK_DAY,
		), $t);
	}

	public function init()
	{

		parent::init();
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Appointment the static model class
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

		return 'appointment';
	}

	/**
	 * defines basic validation rules for any type of appointment, for example date, type or statuss
	 *
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
				['app_status', 'checkStatus', 'on' => 'insert, create, update'],
				['calendarID', 'required', 'on' => 'insert, create, update'],
				['app_user', 'numerical', 'integerOnly' => true, 'allowEmpty' => false],
				['app_subject', 'type', 'type' => 'string'],
				['app_start', 'date', 'allowEmpty' => false, 'format' => ['yyyy-MM-dd H:m:s', 'dd/MM/yyyy', 'dd/MM/yyyy H:m:s', 'yyyy-MM-dd']],
				['app_end', 'date', 'allowEmpty' => true, 'format' => ['yyyy-MM-dd H:m:s', 'dd/MM/yyyy', 'dd/MM/yyyy H:m:s', 'yyyy-MM-dd']],
				['app_end', 'compareDate', 'to' => 'app_start', 'operator' => '>'],
				['app_allday', 'in', 'range' => ['Yes', 'No']],
				array(
						'search, app_id, app_status, app_type, app_subtype, app_subtypeOLD,
						app_notetype, app_start, app_end, app_subject, app_bookedby, app_user,
						calendarID, app_notes, app_session, app_allday, app_private, app_created, app_updated',
						'safe',
						'on' => 'search'
				),
		);
	}

	/**
	 * @todo xtract into validator class
	 *
	 * @param $object
	 * @param $attribute
	 * @return bool
	 * @throws CException
	 * @throws InvalidArgumentException
	 */
	public function compareDate($object, $attribute)
	{

		if (!isset($attribute['to'])) {
			throw new CException("'to' attribute must be defined in compareDate validator");
		}

		if (!isset($this->$attribute['to'])) {
			throw new InvalidArgumentException("attribute '" . $attribute['to'] . "' does not exist in Appointment model");
		}

		$attribute['operator'] = isset($attribute['operator']) ? $attribute['operator'] : '==';

		$compareDate = strtotime($this->$object);
		$compareTo   = strtotime($this->$attribute['to']);

		switch ($attribute['operator']) {
			case '>' :
				if ($compareDate <= $compareTo) {
					$this->addError($object, $this->getAttributeLabel($object) . ' must be more than ' . $this->getAttributeLabel($attribute['to']));
					return false;
				}
				return true;
			case '<' :
				if ($compareDate >= $compareTo) {
					$this->addError($object, $this->getAttributeLabel($object) . ' must be less than ' . $this->getAttributeLabel($attribute['to']));
					return false;
				}
				return true;
			case '>=' :
				if ($compareDate < $compareTo) {
					$this->addError($object, $this->getAttributeLabel($object) . ' must be more or equal than ' . $this->getAttributeLabel($attribute['to']));
					return false;
				}
				return true;
			case '<=' :
				if ($compareDate > $compareTo) {
					$this->addError($object, $this->getAttributeLabel($object) . ' must be less or equal than ' . $this->getAttributeLabel($attribute['to']));
					return false;
				}
				return true;
			default :
				if ($compareDate != $compareTo) {
					$this->addError($object, $this->getAttributeLabel($object) . ' must be equal than ' . $this->getAttributeLabel($attribute['to']));
					return false;
				}
				return true;
		}
	}

	public function deactivate($instantSave = true)
	{

		$this->app_status = self::STATUS_DELETED;
		if ($instantSave) {
			return $this->save(false);
		}
	}

	public function activate($instantSave = true)
	{

		$this->app_status = self::STATUS_ACTIVE;
		if ($instantSave) {
			return $this->save(false);
		}
	}

	public function addTextMessage(Sms $sms)
	{

		$t         = $this->sms;
		$t[]       = $sms;
		$this->sms = $t;
	}

	public function saveTextMessages()
	{
		$sql = "DELETE FROM link_appointment_to_sms WHERE appointmentId = :id";
		Yii::app()->db->createCommand($sql)->execute(['id' => $this->app_id]);
		$sql = [];
		foreach ($this->sms as $sms) {
			$sql[] = '(' . $this->app_id . ', ' . $sms->id . ')';
		}
		if ($sql) {
			$sql = "INSERT INTO link_appointment_to_sms(appointmentId, smsId) VALUES " . implode(', ', $sql);
			return Yii::app()->db->createCommand($sql)->execute();
		}
		return false;
	}

	/**
	 * @return array relational rules.
	 */
	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->app_created = date("Y-m-d H:i:s");
			if (!$this->app_bookedby && isset(Yii::app()->user)) {
				$this->app_bookedby = Yii::app()->user->id;
			}
		}
		$this->app_updated = date("Y-m-d H:i:s");
		$this->app_start   = Date::formatDate('Y-m-d H:i:s', $this->app_start);
		if ($this->app_end) {
			$this->app_end = Date::formatDate('Y-m-d H:i:s', $this->app_end);
		}
		return parent::beforeSave();
	}

	public function relations()
	{

		return array(
				"user"                => array(self::BELONGS_TO, "User", "app_user", 'together' => true),
				"bookedBy"            => array(self::BELONGS_TO, "User", "app_bookedby", 'together' => true),
				"branch"              => array(self::BELONGS_TO, "Branch", "calendarID", 'together' => true),
				"clients"             => array(self::MANY_MANY, "Client", "cli2app(c2a_app, c2a_cli)"),
				"attendees"           => array(self::MANY_MANY, "User", "use2app(u2a_app, u2a_use)"),
				'instructions'        => array(
						self::MANY_MANY,
						'Deal',
						'link_deal_to_appointment(d2a_app, d2a_dea)',
						'select' => array(
								'*',
								'instructions_instructions.d2a_feedback as feedback',
								'instructions_instructions.d2a_id as feedbackId',
								'instructions_instructions.d2a_cv as confirmed',
						), // this is a mirror from instruction. should be careful to avoid circular (recursive) references
						'order'  => 'instructions_instructions.d2a_ord'
				),
				'_instructions'       => array(
						self::MANY_MANY,
						'Deal',
						'link_deal_to_appointment(d2a_app, d2a_dea)',
				),
				'property'            => [self::HAS_MANY, 'Property', array('dea_prop' => 'pro_id'), 'through' => 'instructions'],
				'addresses'           => [self::HAS_MANY, 'Address', array('addressId' => 'id'), 'through' => 'property'],
				'sms'                 => [self::MANY_MANY, 'Sms', 'link_appointment_to_sms(appointmentId, smsId)'],
				'followUpAppointment' => [self::BELONGS_TO, 'Appointment', 'followUpAppointmentId'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
				'app_id'         => 'ID',
				'app_status'     => 'Status',
				'app_type'       => 'Type',
				'app_subtype'    => 'Subtype',
				'app_subtypeOLD' => 'Subtype Old',
				'app_notetype'   => 'Notetype',
				'app_start'      => 'Start',
				'app_end'        => 'End',
				'app_subject'    => 'Subject',
				'app_bookedby'   => 'Bookedby',
				'app_user'       => 'User',
				'calendarID'     => 'Branch',
				'app_notes'      => 'Notes',
				'app_session'    => 'Session',
				'app_allday'     => 'Allday',
				'app_private'    => 'Private',
				'app_created'    => 'Created',
				'app_updated'    => 'Updated',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria = new CDbCriteria;

		$criteria->with = array(
				'clients'       => ['together' => true],
				'addresses'     => ['together' => true],
				'_instructions' => [],
				'user'          => [],
		);
		$this->app_type = (Array)$this->app_type;

		if (in_array(self::TYPE_NOTE, $this->app_type) && $this->app_notetype) {
			$this->app_type = array_diff($this->app_type, [self::TYPE_NOTE]);
			$criteria->addCondition("app_type IN ('" . implode("', '", $this->app_type) . "') OR (app_type = '" . self::TYPE_NOTE . "' AND app_notetype IN ('" . implode("', '", $this->app_notetype) . "'))");
		} else {
			$criteria->compare('app_type', $this->app_type);
		}

		$criteria->compare('app_user', $this->app_user);

		if ($this->app_start) {
			$criteria->compare('app_start', ' >= ' . Date::formatDate('Y-m-d', $this->app_start));
		}
		if ($this->app_end) {
			$criteria->compare('app_end', ' <= ' . Date::formatDate('Y-m-d', $this->app_end));
		}

		$this->searchString = trim($this->searchString);

		if ($this->searchString) {
			$parts       = explode(' ', $this->searchString);
			$addressPart = [];
			$clientPart  = [];
			$subjectPart = [];

			foreach ($parts as $key => $value) {
				$addressPart[]                    = 'addresses.searchString LIKE :part' . $key;
				$clientPart[]                     = 'concat_ws(" ", clients.cli_fname, clients.cli_sname) LIKE :part' . $key;
				$subjectPart[]                    = 'app_subject LIKE :part' . $key;
				$criteria->params[':part' . $key] = '%' . $value . '%';
			}

			$criteria->addCondition(
					 '((' .
					 implode(') AND (', $addressPart) .
					 ')) OR ((' .
					 implode(') AND (', $clientPart) .
					 ')) OR ((' .
					 implode(') AND (', $subjectPart) .
					 '))'
			);

		}

		return new CActiveDataProvider($this, CMap::mergeArray(array(
																	   'criteria' => $criteria,
																	   'sort'     => array(
																			   'defaultOrder' => 'app_start DESC',
																			   'attributes'   => array(
																					   'client'        => [
																							   'asc'  => 'clients.cli_fname',
																							   'desc' => 'clients.cli_fname DESC'
																					   ],
																					   'user.fullName' => [
																							   'asc'  => 'user.use_fname',
																							   'desc' => 'user.use_fname DESC'
																					   ],
																					   'address'       => Array(
																							   'asc'  => 'addresses.searchString',
																							   'desc' => 'addresses.searchString DESC',
																					   ),
																					   '*',
																			   ),

																	   )
															   ), Yii::app()->params['CActiveDataProvider']));
	}

	public function checkStatus($parameter, $arguments)
	{

		return (in_array($parameter, $this->getStatuses()));
	}

	public function getStatuses()
	{

		return array(self::STATUS_ACTIVE, self::STATUS_CANCELLED, self::STATUS_DELETED);
	}

	public function scopes()
	{

		return array(
				'active'    => ['condition' => "app_status = '" . self::STATUS_ACTIVE . "'"],
				'cancelled' => ['condition' => "app_status = '" . self::STATUS_CANCELLED . "'"],
				'deleted'   => ['condition' => "app_status = '" . self::STATUS_DELETED . "'"],
				'passed' => ['condition' => 'app_end <= :passed_app_end', 'params' => ['passed_app_end' => date('Y-m-d H:i:s')]],

		);
	}

	public function fieldsToShow($field = null)
	{

		return false;
	}

	public function getDate()
	{

		if ($this->app_start) {
			return date("d/m/Y", strtotime($this->app_start));
		} else {
			return date("d/m/Y");
		}
	}

	public function setInstructions($instructions, $instantSave = true)
	{

		$instructions = (Array)$instructions;

		if (!$instantSave || $this->isNewRecord && !$this->app_id) {
			$this->attachEventHandler('onAfterSave', function () use ($instructions) {

				static $run = false;
				if (!$run) {
					$this->setInstructions($instructions);
					$run = true;

				}
			});
		} else {
			$sql = "DELETE FROM link_deal_to_appointment WHERE d2a_app = :id";
			Yii::app()->db->createCommand($sql)->execute(['id' => $this->app_id]);
			$t = [];
			foreach ($instructions as $instructionId) {
				$t[] = '(' . $this->app_id . ', ' . $instructionId . ')';
			}
			$sql = "REPLACE INTO link_deal_to_appointment (d2a_app,d2a_dea) VALUES " . implode(',', $t);
			Yii::app()->db->createCommand($sql)->execute();
		}

		$this->instructions = Deal::model()->findAllByPk($instructions);
	}

	public function setClients(Array $clients)
	{
		$this->attachEventHandler('onAfterSave', function () use ($clients) {
			$sql = "DELETE FROM cli2app WHERE c2a_app = :id";
			Yii::app()->db->createCommand($sql)->execute(['id' => $this->app_id]);

			$sql    = [];
			$params = ['id' => $this->app_id];
			sort($clients);
			foreach ($clients as $key => $client) {
				$sql[]                  = "(:id, :client{$key})";
				$params["client{$key}"] = $client;
			}
			if ($sql) {
				$sql = "REPLACE INTO cli2app (c2a_app, c2a_cli) VALUES " . implode(',', $sql);
				Yii::app()->db->createCommand($sql)->execute($params);
			}

		});
	}

	public function DIT($DITStatus = null)
	{
		$criteria = $this->getDbCriteria();
		if ($DITStatus) {
			$criteria->addCondition('DIT = :DIT_status');
			$criteria->params['DIT_status'] = $DITStatus;
		} else {
			$criteria->addCondition('DIT != 0');
		}
		return $this;
	}

	/**
	 * @return Appointment
	 * @throws Exception
	 */
	public function bookDITFollowUp()
	{
		if (!$this->isDIT(self::DIT_BOOKED)) {
			throw new Exception('cannot book DIT follow up for not DIT appointment');
		}

		$followUpApp = new self();

		$followUpApp->attributes = $this->attributes;

		$followUpApp->app_type              = self::TYPE_VIEWING_FOLLOW_UP;
		$followUpApp->app_start             = date("Y-m-d H:i:s", strtotime($this->app_start . ' + 1 day'));
		$followUpApp->app_end               = date("Y-m-d H:i:s", strtotime($this->app_end . ' + 1 day'));
		$followUpApp->app_user              = $this->app_user;
		$followUpApp->app_status            = self::STATUS_ACTIVE;
		$followUpApp->app_bookedby          = $this->app_bookedby;
		$followUpApp->followUpAppointmentId = $this->app_id;

		$instructions = [];
		foreach ($this->instructions as $instruction) {
			$instructions[] = $instruction->dea_id;
		}

		$clients = [];
		foreach ($this->clients as $client) {
			$clients[] = $client->cli_id;
		}

		$followUpApp->setInstructions($instructions);
		$followUpApp->setClients($clients);

		if ($followUpApp->save()) {
			$this->DIT = self::DIT_WITH_FOLLOW_UP;
			$this->save();
		}

		return $followUpApp;

	}

	public function isDIT($DITStatus = null)
	{
		if ($DITStatus === null) {
			return $this->DIT !== 0;
		} else {
			return (int)$this->DIT === (int)$DITStatus;
		}
	}

}