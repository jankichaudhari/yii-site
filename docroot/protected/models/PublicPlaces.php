<?php

/**
 * This is the model class for table "publicPlaces".
 *
 * The followings are the available columns in table 'publicPlaces':
 * @property integer $id
 * @property string $title
 * @property integer $addressId
 * @property string $strapline
 * @property string $description
 * @property integer $mainGalleryImageId
 * @property integer $mainViewImageId
 * @property integer $createdByUserId
 * @property string $createdDT
 * @property integer $modifiedByUserId
 * @property string $modifiedDT
 * @property integer $statusId
 * @property integer $typeId
 */
class PublicPlaces extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PublicPlaces the static model class
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
		return 'publicPlaces';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('addressId, mainGalleryImageId, mainViewImageId, createdByUserId, modifiedByUserId, statusId, typeId', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			array('strapline, description, createdDT, modifiedDT', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, addressId, strapline, description, mainGalleryImageId, mainViewImageId, createdByUserId, createdDT, modifiedByUserId, modifiedDT, statusId, typeId', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'title' => 'Title',
			'addressId' => 'Address',
			'strapline' => 'Strapline',
			'description' => 'Description',
			'mainGalleryImageId' => 'Main Gallery Image',
			'mainViewImageId' => 'Main View Image',
			'createdByUserId' => 'Created By User',
			'createdDT' => 'Created Dt',
			'modifiedByUserId' => 'Modified By User',
			'modifiedDT' => 'Modified Dt',
			'statusId' => 'Status',
			'typeId' => 'Type',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('addressId',$this->addressId);
		$criteria->compare('strapline',$this->strapline,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('mainGalleryImageId',$this->mainGalleryImageId);
		$criteria->compare('mainViewImageId',$this->mainViewImageId);
		$criteria->compare('createdByUserId',$this->createdByUserId);
		$criteria->compare('createdDT',$this->createdDT,true);
		$criteria->compare('modifiedByUserId',$this->modifiedByUserId);
		$criteria->compare('modifiedDT',$this->modifiedDT,true);
		$criteria->compare('statusId',$this->statusId);
		$criteria->compare('typeId',$this->typeId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}