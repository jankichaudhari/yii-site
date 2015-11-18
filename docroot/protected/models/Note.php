<?php

/**
 * This is the model class for table "note".
 *
 * The followings are the available columns in table 'note':
 * @property integer     $not_id
 * @property string      $not_type
 * @property integer     $not_row
 * @property string      $not_blurb
 * @property integer     $not_user
 * @property string      $not_flag
 * @property string      $not_date
 * @property string      $not_edited
 * @property string      $not_status
 *
 *
 * @property User        $creator
 * @property Changelog[] $changes
 */
class Note extends CActiveRecord
{
	const TYPE_CLIENT_GENERAL       = 'client_general';
	const TYPE_CONFIRM              = 'confirm';
	const TYPE_APPOINTMENT          = 'appointment';
	const TYPE_FEEDBACK             = 'feedback';
	const TYPE_SOT                  = 'sot';
	const TYPE_VIEWING_ARRANGEMENTS = 'viewing_arrangements';
	const TYPE_APPOINTMENT_CANCEL   = 'appointment_cancel';
	const TYPE_CLIENT_REQ           = 'client_req';
	const TYPE_DEAL_GENERAL         = 'deal_general';
	const TYPE_DEAL_PRODUCTION      = 'deal_production';
	const TYPE_HIP                  = 'hip';
	const TYPE_VALUATION_FOLLOWUP   = 'valuation_followup';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Note the static model class
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

		return 'note';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('not_blurb', 'required'),
			array('not_row, not_user', 'numerical', 'integerOnly' => true),
			array('not_type', 'length', 'max' => 20),
			array('not_flag', 'length', 'max' => 8),
			array('not_status', 'length', 'max' => 7),
			array('not_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('not_id, not_type, not_row, not_blurb, not_user, not_flag, not_status', 'safe', 'on' => 'search'),
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
			'changes' => [
				self::HAS_MANY, 'Changelog', 'cha_row',
				'on'    => "changes.cha_table = '" . $this->tableName() . "' AND changes.cha_field = 'not_blurb'",
				'order' => 'changes.cha_datetime DESC'
			],
			'creator' => [self::BELONGS_TO, 'User', 'not_user', 'together' => true],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'not_id'     => 'Not',
			'not_type'   => 'Not Type',
			'not_row'    => 'Not Row',
			'not_blurb'  => 'Not Blurb',
			'not_user'   => 'Not User',
			'not_flag'   => 'Not Flag',
			'not_date'   => 'Not Date',
			'not_edited' => 'Not Edited',
			'not_status' => 'Not Status',
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

		$criteria->compare('not_id', $this->not_id);
		$criteria->compare('not_type', $this->not_type, true);
		$criteria->compare('not_row', $this->not_row);
		$criteria->compare('not_blurb', $this->not_blurb, true);
		$criteria->compare('not_user', $this->not_user);
		$criteria->compare('not_flag', $this->not_flag, true);
		$criteria->compare('not_date', $this->not_date, true);
		$criteria->compare('not_edited', $this->not_edited, true);
		$criteria->compare('not_status', $this->not_status, true);

		return new CActiveDataProvider($this, ['criteria' => $criteria]);
	}

	public function defaultScope()
	{

		return [];
//		return ['condition' => "not_status = 'Active'"];
	}

	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->not_date = date("Y-m-d H:i:s");
			$this->not_user = Yii::app()->user->getId();
		} else if ($this->not_blurb) {
			/** @var $oldRecord Note */
			$oldRecord = $this->findByPk($this->not_id); // retrieve old version of that record;
			if ($oldRecord->not_blurb !== $this->not_blurb) {
				$change              = new Changelog();
				$change->cha_session = session_id();
				$change->cha_table   = $this->tableName();
				$change->cha_old     = $oldRecord->not_blurb;
				$change->cha_new     = $this->not_blurb;
				$change->cha_field   = 'not_blurb';
				$change->cha_row     = $this->not_id;
				$change->cha_action  = Changelog::ACTION_UPDATE;
				$change->save();
			}
		}
		$this->not_edited = date("Y-m-d H:i:s");
		return parent::beforeSave();
	}

	public static function getTypes()
	{

		return array_combine($t = array(
			self::TYPE_CONFIRM,
			self::TYPE_APPOINTMENT,
			self::TYPE_FEEDBACK,
			self::TYPE_SOT,
			self::TYPE_VIEWING_ARRANGEMENTS,
			self::TYPE_APPOINTMENT_CANCEL,
			self::TYPE_CLIENT_REQ,
			self::TYPE_DEAL_GENERAL,
			self::TYPE_CLIENT_GENERAL,
			self::TYPE_DEAL_PRODUCTION,
			self::TYPE_HIP,
			self::TYPE_VALUATION_FOLLOWUP,
		), $t);
	}

	public function deleteNotesHavingEmptyTypeId($type = "")
	{
		if ($type) {
			$criteria = new CDbCriteria();
			$criteria->compare('not_type', $type);
			$criteria->addcondition("not_row is null OR not_row = 0");
			$notes             = Note::model()->findAll($criteria);
			foreach($notes as $note){
				$note->not_status = 'Deleted';
				$note->save();
			}
		}
	}

	public function saveNoteTypeIds($noteIds = [], $noteTypeId)
	{

		if (!$noteTypeId) {
			throw new CHttpException(404, "Note type is not found");
		}
		$r = Note::model()->updateAll(['not_row' => $noteTypeId], "not_id in (" . implode(",", $noteIds) . ")");
	}

	public function saveNote($notes = [], $noteTypeId)
	{

		$noteType  = (isset($notes['not_type']) && $notes['not_type']) ? $notes['not_type'] : '';
		$noteBlurb = (isset($notes['not_blurb']) && $notes['not_blurb']) ? $notes['not_blurb'] : '';

		if ($noteType && $noteBlurb) {
			$note            = new Note();
			$note->not_row   = $noteTypeId;
			$note->not_blurb = $noteBlurb;
			$note->not_type  = $noteType;
			$note->save();
		}
	}

}