<?php

/**
 * This is the model class for table "transportStations".
 *
 * The followings are the available columns in table 'transportStations':
 * @TransportStations integer $id
 * @TransportStations integer $typeId
 * @TransportStations string  $title
 * @TransportStations string  $description
 * @TransportStations double  $latitude
 * @TransportStations double  $longitude
 * @TransportStations integer $createdBy
 * @TransportStations string  $createdDt
 * @TransportStations integer $modifiedBy
 * @TransportStations string  $modifiedDt
 * @TransportStations integer $statusId
 * @TransportStations TransportTypes $transportTypes
 * @TransportStations User $user
 */
class TransportStations extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TransportStations the static model class
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
		return 'transportStations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('createdBy, modifiedBy, statusId', 'numerical', 'integerOnly'=> true),
			array('latitude, longitude', 'numerical'),
			array('title', 'unique', 'on' => 'update'),
			array('title', 'length', 'max'=> 255),
			array('description, createdDt, modifiedDt', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, description, latitude, longitude, createdBy, createdDt, modifiedBy, modifiedDt, statusId', 'safe', 'on'=> 'search'),
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
			'user'=> array(self::BELONGS_TO, "User", "modifiedBy",'together'=>true),
			'transportTypes'  => array(self::MANY_MANY, 'TransportTypes', 'link_transportStations_to_transportTypes(transportStation, transportType)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'          => 'ID',
//			'typeId'      => 'Type',
			'title'       => 'Title',
			'description' => 'Description',
			'latitude'    => 'Latitude',
			'longitude'   => 'Longitude',
			'createdBy'   => 'Created By',
			'createdDt'   => 'Created Dt',
			'modifiedBy'  => 'Modified By',
			'modifiedDt'  => 'Modified Dt',
			'statusId'    => 'Status',
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

		$criteria->scopes = 'onlyActive';
		$criteria->compare('id', $this->id);
//		$criteria->compare('typeId', $this->typeId);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('latitude', $this->latitude);
		$criteria->compare('longitude', $this->longitude);
		$criteria->compare('createdBy', $this->createdBy);
		$criteria->compare('createdDt', $this->createdDt, true);
		$criteria->compare('modifiedBy', $this->modifiedBy);
		$criteria->compare('modifiedDt', $this->modifiedDt, true);
		$criteria->compare('statusId', $this->statusId);

		return new CActiveDataProvider($this, array(
												   'criteria'=> $criteria,
											  ));
	}

	public function scopes()
	{
		return array(
			'onlyActive' => array('condition' => "statusId = '1'"),
		);
	}

	/*
	   *
	   */
	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->createdDt = date("Y-m-d H:i:s");
			$this->createdBy = Yii::app()->user->getId();
			$this->statusId = 2;
			$unTitles = TransportStations::model()->findAll("title LIKE 'untitled%'");

			$unTitleCount = (count($unTitles)!=0) ? (count($unTitles)+1) : 1;
			$this->title = 'untitled-' . $unTitleCount;
		}

		$this->modifiedDt    = date("Y-m-d H:i:s");
		$this->modifiedBy = Yii::app()->user->getId();
		return true;
	}

//	protected function afterSave()
//	{
//		parent::afterSave();
//		TransportStations::model()->updateByPk($this->id,['title'=>$this->title.$this->id]);
//		return true;
//	}

	public function transportStationBelongsToTypes($typeId){
		static $TransportTypes;
		if ($TransportTypes === null) {
			$TransportTypes = array();
			foreach ($this->transportTypes as $type) {
//				$TransportTypes[] = $type->id;
				array_push($TransportTypes,$type->id);
			}
		}
		return in_array($typeId, $TransportTypes);
	}
}