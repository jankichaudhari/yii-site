<?php

/**
 * This is the model class for table "link_instruction_to_feature".
 *
 * The followings are the available columns in table 'link_instruction_to_feature':
 * @property integer $featureId
 * @property integer $dealId
 */
class LinkInstructionToFeature extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LinkInstructionToFeature the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	/**
	 * @param $from int instruction id to copy from
	 * @param $to   int instruction id to copy to
	 * @return int
	 */
	public static function copyRecords($from, $to)
	{

		$sql = "REPLACE INTO link_instruction_to_feature (featureId, dealId) SELECT featureId, :to FROM link_instruction_to_feature WHERE dealId=:from";
		return Yii::app()->db->createCommand($sql)->execute(array(
																	':to'   => $to,
																	':from' => $from,
															));
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'link_instruction_to_feature';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
				array('featureId, dealId', 'numerical', 'integerOnly' => true),
				array('featureId, dealId', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
				'featureId' => 'Feature',
				'dealId'    => 'Deal',
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