<?php

/**
 * This is the model class for table "link_deal_to_appointment".
 *
 * The followings are the available columns in table 'link_deal_to_appointment':
 * @property integer     $d2a_id
 * @property integer     $d2a_dea
 * @property integer     $d2a_app
 * @property integer     $d2a_ord
 * @property string      $d2a_cv
 * @property string      $d2a_cvnotes
 * @property string      $d2a_feedback
 * @property string      $d2a_feedbacknotes
 * @property Appointment $appointment
 * @property Deal        $deal
 */
class LinkDealToAppointment extends CActiveRecord
{

	const FEEDBACK_POSITIVE          = 'Positive';
	const FEEDBACK_INDIFFERENT       = 'Indifferent';
	const FEEDBACK_NEGATIVE          = 'Negative';
	const FEEDBACK_NONE              = 'None';
	const CONFIRMATION_NOT_CONFIRMED = 'Not Confirmed';
	const CONFIRMATION_MESSAGE_LEFT  = 'Message Left';
	const CONFIRMATION_CONFIRMED     = 'Confirmed';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LinkDealToAppointment the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	public static function getPossibleOutcomes()
	{

		return array_combine($t = array(
			self::FEEDBACK_POSITIVE,
			self::FEEDBACK_INDIFFERENT,
			self::FEEDBACK_NEGATIVE,
		), $t);
	}

	private static function getFeedbackValues()
	{

		return array_combine($t = array(
			self::FEEDBACK_NONE,
			self::FEEDBACK_POSITIVE,
			self::FEEDBACK_INDIFFERENT,
			self::FEEDBACK_NEGATIVE,
		), $t);
	}

	private static function getConfirmationStatuses()
	{

		return array_combine($t = array(
			self::CONFIRMATION_NOT_CONFIRMED,
			self::CONFIRMATION_MESSAGE_LEFT,
			self::CONFIRMATION_CONFIRMED,
		), $t);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'link_deal_to_appointment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('d2a_cvnotes, d2a_feedbacknotes', 'type', 'type' => 'string'),
			array('d2a_dea, d2a_app, d2a_ord', 'numerical', 'integerOnly' => true),
			array('d2a_cv', 'in', 'range' => self::getConfirmationStatuses()),
			array('d2a_feedback', 'in', 'range' => self::getFeedbackValues()),
			array('d2a_id, d2a_dea, d2a_app, d2a_ord, d2a_cv, d2a_cvnotes, d2a_feedback, d2a_feedbacknotes', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'appointment' => array(self::BELONGS_TO, 'Appointment', 'd2a_app'),
			'deal'        => array(self::BELONGS_TO, 'Deal', 'd2a_dea'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'd2a_id'            => 'id',
			'd2a_dea'           => 'Dea',
			'd2a_app'           => 'App',
			'd2a_ord'           => 'Ord',
			'd2a_cv'            => 'Cv',
			'd2a_cvnotes'       => 'Cvnotes',
			'd2a_feedback'      => 'Feedback',
			'd2a_feedbacknotes' => 'Feedbacknotes',
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
}