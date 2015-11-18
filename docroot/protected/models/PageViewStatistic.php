<?php

/**
 * This is the model class for table "pageViewStatistic".
 *
 * The followings are the available columns in table 'pageViewStatistic':
 * @property string $id
 * @property string $page
 * @property string $lastAccessed
 * @property string $lastViewId
 */
class PageViewStatistic extends CActiveRecord
{
	public $viewCount = '';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PageViewStatistic the static model class
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

		return 'stat_pageViewStatistic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('page, lastAccessed, lastViewId', 'required'),
			array('page', 'length', 'max'=> 255),
			array('viewCount, lastViewId', 'length', 'max'=> 10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, page, viewCount, lastAccessed, lastViewId', 'safe', 'on'=> 'search'),
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
			'id'           => 'ID',
			'page'         => 'Page',
			'viewCount'    => 'View Count',
			'lastAccessed' => 'Last Accessed',
			'lastViewId'   => 'Last View',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(CDbCriteria $criteria = null)
	{

		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		if ($criteria) {
			$criteria = new CDbCriteria($criteria->toArray());
		} else {
			$criteria = new CDbCriteria;
		}

		$criteria->compare('id', $this->id, true);
		$criteria->compare('page', $this->page, true);
		$criteria->compare('viewCount', $this->viewCount, true);
		$criteria->compare('lastAccessed', $this->lastAccessed, true);
		$criteria->compare('lastViewId', $this->lastViewId, true);


		return new CActiveDataProvider($this, array(
												   'criteria'=> $criteria,
												   'pagination' => array('pageSize' => 37),
											  ));
	}
}