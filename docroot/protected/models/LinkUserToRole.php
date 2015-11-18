<?php

/**
 * This is the model class for table "link_user_to_role".
 *
 * The followings are the available columns in table 'link_user_to_role':
 * @property integer $u2r_id
 * @property integer $u2r_use
 * @property integer $u2r_rol
 */
class LinkUserToRole extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserToRole the static model class
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
		return 'link_user_to_role';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u2r_use, u2r_rol', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('u2r_id, u2r_use, u2r_rol', 'safe', 'on'=>'search'),
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
			'u2r_id' => 'U2r',
			'u2r_use' => 'U2r Use',
			'u2r_rol' => 'U2r Rol',
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

		$criteria->compare('u2r_id',$this->u2r_id);
		$criteria->compare('u2r_use',$this->u2r_use);
		$criteria->compare('u2r_rol',$this->u2r_rol);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}