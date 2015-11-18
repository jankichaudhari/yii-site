<?php

/**
 * This is the model class for table "instructionToPdfSettings".
 *
 * The followings are the available columns in table 'instructionToPdfSettings':
 * @property integer $id
 * @property integer $instructionId
 * @property integer $displayLeaseExpires
 * @property integer $displayServiceCharge
 * @property integer $displayGroundRent
 * @property string  $additionalNotes
 */
class InstructionToPdfSettings extends CActiveRecord
{
	public $displayLeaseExpires = 1;
	public $displayServiceCharge = 1;
	public $displayGroundRent = 1;
	public $additionalNotes = '';

	public function behaviors()
	{

		return array(
			'createdModified' => array(
				'class'           => 'application.components.behaviours.CreatedModifiedBehavior',
				'createdField'    => 'created',
				'createdByField'  => 'createdBy',
				'modifiedField'   => 'modified',
				'modifiedByField' => 'modifiedBy',
			)
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return InstructionToPdfSettings the static model class
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

		return 'instructionToPdfSettings';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('instructionId', 'required'),
			array('instructionId, displayLeaseExpires, displayServiceCharge, displayGroundRent', 'numerical', 'integerOnly' => true),
			array('additionalNotes', 'safe'),
			array('id, instructionId, displayLeaseExpires, displayServiceCharge, displayGroundRent, additionalNotes', 'safe', 'on' => 'search'),

		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'instruction' => [self::BELONGS_TO, 'Deal', 'instructionId'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'                   => 'ID',
			'instructionId'        => 'Instruction',
			'displayLeaseExpires'  => 'Display Lease Expires',
			'displayServiceCharge' => 'Display Service Charge',
			'displayGroundRent'    => 'Display Ground Rent',
			'additionalNotes'      => 'Additional Notes',
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
		$criteria->compare('instructionId', $this->instructionId);
		$criteria->compare('displayLeaseExpires', $this->displayLeaseExpires);
		$criteria->compare('displayServiceCharge', $this->displayServiceCharge);
		$criteria->compare('displayGroundRent', $this->displayGroundRent);
		$criteria->compare('additionalNotes', $this->AdditionalNotes, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}
}