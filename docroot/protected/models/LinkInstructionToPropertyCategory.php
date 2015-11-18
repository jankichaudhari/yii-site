<?php

/**
 * This is the model class for table "link_instruction_to_propertyCategory".
 *
 * The followings are the available columns in table 'link_instruction_to_propertyCategory':
 * @property integer $id
 * @property integer $instructionId
 * @property integer $categoryId
 */
class LinkInstructionToPropertyCategory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LinkInstructionToPropertyCategory the static model class
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
		return 'link_instruction_to_propertyCategory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
				['instructionId, categoryId', 'numerical', 'integerOnly' => true],
				['id, instructionId, categoryId', 'safe', 'on' => 'search'],
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
				'instruction' => [self::BELONGS_TO, 'Deal', 'instructionId', 'together' => true]
		);
	}

	public function scopes()
	{
		return array(
				'publicAvailableInstruction' => array(
						'with'      => ['instruction'],
						'condition' => "instruction.dea_status IN ('Available','Under Offer','Under Offer With Other','Exchanged') OR instruction.displayOnWebsite = 1",
				)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'id'            => 'ID',
				'instructionId' => 'Instruction',
				'categoryId'    => 'Category',
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