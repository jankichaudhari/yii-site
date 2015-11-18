<?php

/**
 * This is the model class for table "localEvent".
 *
 * The followings are the available columns in table 'localEvent':
 * @property string             $id
 * @property string             $strapline
 * @property string             $heading
 * @property string             $description
 * @property string             $dateFrom
 * @property string             $dateTo
 * @property string             $timeFrom
 * @property string             $timeTo
 * @property string             $url
 * @property string             $addressID
 * @property integer            $createdBy
 * @property string             $created
 * @property int                $mainImageID
 * @property string             $linkId
 * @property int                $status
 * @property Lists              $statusValue
 *
 * @property LocalEventImage    $mainImage
 * @property LocalEventImage[]  $images
 * @property Location           $address
 *
 * @mnethod LocalEvent onlyActive()
 */
class LocalEvent extends CActiveRecord
{
	public $LocalEventStatus = 'LocalEventStatus';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LocalEvent the static model class
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

		return 'localEvent';
	}

	protected function afterFind()
	{

		if ($this->timeFrom) $this->timeFrom = date("H:i", strtotime($this->timeFrom));
		if ($this->timeTo) $this->timeTo = date("H:i", strtotime($this->timeTo));

		parent::afterFind();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(

			array('description, heading', 'required'),
			array('heading', 'unique'),
			array(
				'strapline, heading, url', 'length',
				'max' => 255
			),
			array(
				'timeFrom, timeTo', 'date',
				'format' => 'H:m'
			),
			array(
				'dateFrom, dateTo', 'date',
				'format' => 'dd/MM/yyyy'
			),
			array('mainImageID, status', 'numerical'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, strapline, heading, description, dateFrom, dateTo, url, createdBy', 'safe',
				'on' => 'search'
			),
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
			'address'     => array(self::BELONGS_TO, 'Location', 'addressID'),
			'images'      => array(
				self::HAS_MANY, 'LocalEventImage', 'recordId',
				'on'    => "recordType = 'LocalEvent'",
				'order' => "images.displayOrder ASC"
			),
			'mainImage'   => array(self::BELONGS_TO, 'LocalEventImage', 'mainImageID'),
			'statusValue' => array(
				self::BELONGS_TO, "Lists", array("LocalEventStatus" => 'ListName', 'status' => 'ListItemID')
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'          => 'ID',
			'strapline'   => 'Strapline',
			'heading'     => 'Heading',
			'description' => 'Description',
			'dateFrom'    => 'Date From',
			'dateTo'      => 'Date To',
			'timeFrom'    => 'Time From',
			'timeTo'      => 'Time To',
			'url'         => 'Url',
			'addressID'   => 'Address',
			'createdBy'   => 'Created By',
			'created'     => 'Created',
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

		$criteria       = new CDbCriteria;
		$criteria->with = array("mainImage");

		$criteria->compare('id', $this->id, true);
		$criteria->compare('strapline', $this->strapline, true);
		$criteria->compare('heading', $this->heading, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('dateFrom', $this->dateFrom, true);
		$criteria->compare('dateTo', $this->dateTo, true);
		$criteria->compare('timeFrom', $this->timeFrom, true);
		$criteria->compare('timeTo', $this->timeTo, true);
		$criteria->compare('url', $this->url, true);
		$criteria->compare('addressID', $this->addressID, true);
		$criteria->compare('createdBy', $this->createdBy);
		$criteria->compare('created', $this->created, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
												   //												   'with' => array('mainImage')
											  ));
	}

	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->created   = date("Y-m-d H:i:s");
			$this->createdBy = Yii::app()->user->getId();
		}

		if ($this->dateFrom) {
			$this->dateFrom = Date::formatDate("Y-m-d", $this->dateFrom);
		} else {
			$this->dateFrom = null;
		}
		if ($this->dateTo) {
			$this->dateTo = Date::formatDate("Y-m-d", $this->dateTo);
		} else {
			$this->dateTo = null;
		}

		$this->linkId = $this->getUniqLinkId($this->heading);

		if (!$this->timeFrom) $this->timeFrom = null;
		if (!$this->timeTo) $this->timeTo = null;

		return true;
	}

	private function getUniqLinkId($heading)
	{

		return str_replace(" ", "-", $heading) . "-" . date("Y");
	}

	public function getDate($wtime = true)
	{

		$string = '';
		$string .= $this->dateFrom ? Date::formatDate(($this->dateTo ? "" : "D ") . "j M", $this->dateFrom) : '';
		$string .= $this->dateFrom && $this->dateTo ? Date::formatDate(" - d M", $this->dateTo) : "";
		$string .= $this->dateTo || $this->dateFrom ? Date::formatDate(" Y", ($this->dateTo ? $this->dateTo : $this->dateFrom)) : "";
		if ($wtime) {
			$string .= ' ' . $this->getTime();
		}

		return $string;

	}

	public function getTime()
	{

		$string = '';
		if ($this->timeFrom && Date::formatDate("i", $this->timeFrom) > 0) {
			$dateFormat = "g:i a";
		} else {
			$dateFormat = "g a";
		}
		$string .= $this->timeFrom ? "From " . Date::formatDate($dateFormat, $this->timeFrom) : "";
		$string .= $this->timeTo ? " to " . Date::formatDate($dateFormat, $this->timeTo) : "";
		return $string;
	}

	public function scopes()
	{

		return array(
			'onlyActive' => ['condition' => 'status = 3'],
			'published'  => ['condition' => "dateTo >= '" . date("Y-m-d") . "' OR (dateTo is NULL AND dateFrom >= '" . date("Y-m-d") . "')"]
		);
	}

}