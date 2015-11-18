<?php

/**
 * This is the model class for table "lists".
 *
 * The followings are the available columns in table 'lists':
 * @property string $ListName
 * @property string $ListOrder
 * @property string $ListItem
 * @property string $ListItemID
 * @property string $Notes
 */
class Lists extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Lists the static model class
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
		return 'lists';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ListName, ListItem, Notes', 'length', 'max'=>255),
			array('ListOrder, ListItemID', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ListName, ListItem, Notes', 'safe', 'on'=>'search'),
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
			'ListName' => 'List Name',
			'ListOrder' => 'List Order',
			'ListItem' => 'List Item',
			'ListItemID' => 'List Item',
			'Notes' => 'Notes',
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

		$criteria->compare('ListName',$this->ListName,true);
		$criteria->compare('ListOrder',$this->ListOrder,true);
		$criteria->compare('ListItem',$this->ListItem,true);
		$criteria->compare('Notes',$this->Notes,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getList($listName){
		/** @var $data Lists[] */
		$data = $this->findAll("ListName='" . $listName . "'");

		$result = array();
		foreach ($data as $value) {
			$result[$value->ListItemID] = $value->ListItem;
		}

		return $result;

	}
}