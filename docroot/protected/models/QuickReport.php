<?php

/**
 * This is the model class for table "quickReport".
 *
 * The followings are the available columns in table 'quickReport':
 * @property string $name
 * @property string $title
 * @property string $query
 * @property string $actionLink
 * @property string $description
 * @property string $keyField
 * @property string $isActive
 *
 *@method QuickReport active()
 */
class QuickReport extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QuickReport the static model class
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
		return 'quickReport';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			['title, query, keyField', 'required'],
			['isActive', 'boolean'],
			['actionLink', 'url'],
			['title', 'length', 'max' => 255],
			['title, query, actionLink, description', 'safe', 'on' => 'search'],
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'        => 'Name',
			'title'       => 'Title',
			'query'       => 'Query',
			'actionLink'  => 'Action Link',
			'description' => 'Description',
			'isActive'    => 'Active',
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
		$this->name = str_replace(" ", '-', $this->title);
		return parent::beforeSave();
	}

	public function scopes()
	{
		return ['active' => ['condition' => 'isActive = 1']];
	}

}