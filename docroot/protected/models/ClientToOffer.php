<?php

/**
 * This is the model class for table "cli2off".
 *
 * The followings are the available columns in table 'cli2off':
 * @property integer $c2o_id
 * @property integer $c2o_cli
 * @property integer $c2o_off
 */
class ClientToOffer extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ClientToOffer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cli2off';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('c2o_cli, c2o_off', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('c2o_id, c2o_cli, c2o_off', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'c2o_id' => 'C2o',
			'c2o_cli' => 'C2o Cli',
			'c2o_off' => 'C2o Off',
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

		$criteria=new CDbCriteria;

		$criteria->compare('c2o_id',$this->c2o_id);
		$criteria->compare('c2o_cli',$this->c2o_cli);
		$criteria->compare('c2o_off',$this->c2o_off);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}