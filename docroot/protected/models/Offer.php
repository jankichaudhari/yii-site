<?php

/**
 * This is the model class for table "offer".
 *
 * The followings are the available columns in table 'offer':
 * @property integer  $off_id
 * @property string   $off_timestamp
 * @property integer  $off_deal
 * @property integer  $off_neg
 * @property integer  $off_price
 * @property string   $off_conditions
 * @property string   $off_date
 * @property string   $off_status
 * @property string   $off_notes
 * @property integer  $off_app
 * @property User     $negotiator
 * @property Client[] $clients
 * @property Deal     instruction
 *
 * @method Offer nonDeleted()
 */
class Offer extends CActiveRecord
{
	const STATUS_ACCEPTED      = "Accepted";
	const STATUS_DELETED       = "Deleted";
	const STATUS_SUBMITTED     = "Submitted";
	const STATUS_REJECTED      = "Rejected";
	const STATUS_NOT_SUBMITTED = "Not Submitted";
	const STATUS_WITHDRAWN     = "Withdrawn";

	public $off_price = '';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Offer the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	public static function getStatuses()
	{

		return array_combine($t = array(
			self::STATUS_NOT_SUBMITTED,
			self::STATUS_SUBMITTED,
			self::STATUS_ACCEPTED,
			self::STATUS_REJECTED,
			self::STATUS_WITHDRAWN,
			self::STATUS_DELETED,

		), $t);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'offer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('off_price', 'unsafe', 'on' => 'update'),
			array('off_price', 'numerical', 'integerOnly' => true, 'min' => 1, 'allowEmpty' => false, 'tooSmall' => 'Price cannot be less than 1', 'message' => 'Asking Price must be a number', 'on' => 'insert'),
			array('off_deal', 'required', 'message' => 'Instruction is not specified'),
			array('off_deal, off_neg, off_app', 'numerical', 'integerOnly' => true),
			array('off_status', 'length', 'max' => 13),
			array('off_date,off_conditions, off_notes', 'safe'),
			array('off_id, off_timestamp, off_deal, off_neg, off_price, off_conditions, off_date, off_status, off_notes, off_app', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'instruction'       => array(self::BELONGS_TO, "Deal", "off_deal"),
			'negotiator'        => array(self::BELONGS_TO, "User", "off_neg"),
			'clients'           => array(
				self::MANY_MANY, 'Client', 'cli2off(c2o_off,c2o_cli)',
				'together' => true,
				'joinType' => 'inner join',
				'select'   => array('*',),
			),
			'conditionsChanges' => [
				self::HAS_MANY, 'Changelog', 'cha_row', 'on' => "conditionsChanges.cha_table = '" . $this->tableName() . "' AND cha_field = 'off_conditions' ",
				'order'                                      => 'conditionsChanges.cha_datetime DESC'
			],
			'notesChanges'      => [
				self::HAS_MANY, 'Changelog', 'cha_row', 'on' => "notesChanges.cha_table = '" . $this->tableName() . "' AND cha_field = 'off_notes' ",
				'order'                                      => 'notesChanges.cha_datetime DESC'
			],
		);
	}

	public function setClients(Array $clients, $instantSave = true)
	{

		if ($instantSave && (!$this->isNewRecord || $this->off_id)) {
			$sql = "DELETE FROM cli2off WHERE c2o_off = " . $this->off_id;
			Yii::app()->db->createCommand($sql)->execute();
			$sql = [];
			foreach ($clients as $value) {
				$sql[] = "('" . $this->off_id . "', '" . $value . "')";
			}
			if ($sql) {
				$sql = "REPLACE INTO cli2off (c2o_off, c2o_cli) VALUES " . implode(',', $sql) . "";
				Yii::app()->db->createCommand($sql)->execute();
			}
		} else {
			$run = false;
			$this->attachEventHandler('onAfterSave', function (CEvent $event) use ($clients, &$run) {

				if (!$run) {
					$event->sender->setClients($clients);
					$run = true;
				}
			});
		}

		$this->clients = Client::model()->findAllByPk($clients);
	}

	public function deactivate()
	{

		$this->off_status = self::STATUS_DELETED;
		return $this->save(false);
	}

	public function restore()
	{

		$this->off_status = self::STATUS_NOT_SUBMITTED;
		return $this->save(false);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'off_id'         => 'Id',
			'off_timestamp'  => 'Date',
			'off_deal'       => 'Deal',
			'off_neg'        => 'Negotiator',
			'off_price'      => 'Asking Price',
			'off_conditions' => 'Conditions',
			'off_date'       => 'Date',
			'off_status'     => 'Status',
			'off_notes'      => 'Notes',
			'off_app'        => 'App',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria = new CDbCriteria;
		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}

	protected function beforeSave()
	{

		if ($this->off_status == self::STATUS_ACCEPTED) {
			$statuses = [Offer::STATUS_ACCEPTED,Offer::STATUS_NOT_SUBMITTED,Offer::STATUS_REJECTED,Offer::STATUS_SUBMITTED];
			$this->updateAll(
				 ['off_status' => Offer::STATUS_REJECTED],
				 "off_deal = '" . $this->off_deal . "' AND off_status IN ('" . implode("','",$statuses) . "')"
			);
		}

		if (!$this->isNewRecord) {
			/** @var $oldRecord Offer */
			$oldRecord = $this->findByPk($this->off_id); // retrieve old version of that record;
			foreach ($this->attributeLabels() as $eachAttr => $eachLabel) {
				if ((!empty($this->$eachAttr)) && ($oldRecord->$eachAttr !== $this->$eachAttr)) {
					$change              = new Changelog();
					$change->cha_session = session_id();
					$change->cha_table   = $this->tableName();
					$change->cha_old     = $oldRecord->$eachAttr;
					$change->cha_new     = $this->$eachAttr;
					$change->cha_field   = $eachAttr;
					$change->cha_row     = $this->off_id;
					$change->cha_action  = Changelog::ACTION_UPDATE;
					$change->save();
				}
			}
		}
		$this->off_timestamp = date("Y-m-d H:i:s");
		return parent::beforeSave();
	}

	public function scopes()
	{

		return array('nonDeleted' => array('condition' => "off_status <> 'Deleted'"));
	}
}