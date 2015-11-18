<?php
use \application\models\Place\GalleryImage as GalleryImage;
use \application\models\Place\MainGalleryImage as MainGalleryImage;
use \application\models\Place\MainViewImage as MainViewImage;

/**
 * This is the model class for table "publicPlaces".
 *
 * The followings are the available columns in table 'publicPlaces':
 * @property integer                                  $id
 * @property string                                   $title
 * @property integer                                  $addressId
 * @property string                                   $strapline
 * @property string                                   $description
 * @property integer                                  $createdByUserId
 * @property string                                   $createdDT
 * @property integer                                  $modifiedByUserId
 * @property string                                   $modifiedDT
 * @property string                                   $statusId
 * @property string                                   $typeId
 * @property  User                                    $creator
 * @property User                                     $lastModifier
 * @property Location                                 $location
 * @property \application\models\Place\GalleryImage[] $images
 * @property \application\models\Place\GalleryImage   $mainGalleryImageId
 * @property \application\models\Place\GalleryImage   $mainViewImageId
 *
 *
 */
class Place extends CActiveRecord
{
	public $statusId = 4;
	public $typeId = 1;
	public $title = "";
	public $address = "";
	public $PublicPlacesStatus = "PublicPlacesStatus";
	public $PublicPlacesParkType = "PublicPlacesParkType";

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Place the static model class
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

		return 'publicPlaces';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('title', 'required'),
			array('title', 'unique'),
			array('addressId, createdByUserId, modifiedByUserId', 'numerical', 'integerOnly' => true),
			array('title', 'length', 'max' => 100),
			array('strapline', 'length', 'max' => 200),
			array('statusId', 'length', 'max' => 1),
			array('description,createdDT, statusId, modifiedDT', 'safe'),
			array('statusId, typeId', 'numerical'),
			array('strapline, typeId,  description ', 'required', 'on' => 'goPlaceLive'),
			array('mainViewImageId', 'required', 'message' => '{attribute} must be uploaded', 'on' => 'goPlaceLive'),
			array(
				'mainViewImageId', 'numerical', 'min' => 1, 'tooSmall' => '{attribute} must be uploaded',
				'on'                                  => 'goPlaceLive'
			),
			array('description', 'length', 'min' => 200, 'on' => 'goPlaceLive'),
			array(
				'id, title, addressId, strapline, description, createdByUserId, createdDT, modifiedByUserId, modifiedDT, statusId, typeId',
				'safe', 'on' => 'search'
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'location'         => array(self::BELONGS_TO, 'Location', 'addressId'),
			'images'           => array(
				self::HAS_MANY, '\application\models\Place\GalleryImage', 'recordId', 'on' => "recordType = 'Place'",
				'order'                                                                    => "displayOrder ASC"
			),
			'mainGalleryImage' => array(
				self::BELONGS_TO, '\application\models\Place\MainGalleryImage', 'mainGalleryImageId'
			),
			'mainViewImage'    => array(self::BELONGS_TO, '\application\models\Place\MainViewImage', 'mainViewImageId'),
			'statusValue'      => array(
				self::BELONGS_TO, "Lists", array("PublicPlacesStatus" => 'ListName', 'statusId' => 'ListItemID')
			),
			'placeType'        => array(
				self::BELONGS_TO, "Lists", array("PublicPlacesParkType" => 'ListName', 'typeId' => 'ListItemID')
			),
			'creator'          => array(self::BELONGS_TO, "User", 'createdByUserId'),
			'lastModifier'     => array(self::BELONGS_TO, "User", 'modifiedByUserId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'                 => 'ID',
			'title'              => 'Title',
			'addressId'          => 'Address',
			'strapline'          => 'Strapline',
			'description'        => 'Description',
			'mainGalleryImageId' => 'Main Page Large Photo',
			'mainViewImageId'    => 'Listing Page Photo',
			'createdByUserId'    => 'Created By User',
			'createdDT'          => 'Created Dt',
			'modifiedByUserId'   => 'Modified By User',
			'modifiedDT'         => 'Modified Dt',
			'statusId'           => 'Status',
			'typeId'             => 'Place Type',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @param CDbCriteria $criteria
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(CDbCriteria $criteria = null)
	{

		$criteria       = $criteria ? clone $criteria : new CDbCriteria;
		$criteria->with = array('location' => array('together' => true));
		$criteria->compare('title', $this->title, true);
		$criteria->compare("concat_ws(' ', location.address, location.city, location.postcode)", $this->address, true, 'OR');
		$criteria->compare('statusId', $this->statusId, true);
		if ($this->typeId != 1) {
			$criteria->compare('typeId', $this->typeId, true);
		}

		return new CActiveDataProvider($this, array(
												   'pagination' => array('pageSize' => 18),
												   'criteria'   => $criteria
											  ), Yii::app()->params['CActiveDataProvider']);
	}

	/*
	 *
	 */
	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->createdDT       = date("Y-m-d H:i:s");
			$this->createdByUserId = Yii::app()->user->getId();
		}

		$this->modifiedDT       = date("Y-m-d H:i:s");
		$this->modifiedByUserId = Yii::app()->user->getId();
		return true;
	}

	protected function beforeDelete()
	{

		//delete images for these records if uploaded
		$placeInfo = Place::model()->find('createdByUserId=' . Yii::app()->user->getId() . ' AND title is NULL');
		if (count($placeInfo) > 0) {
			$fileInfos = File::model()->findAll("recordId=" . $placeInfo->id . " AND recordType='Place'");
			if (count($fileInfos) > 0) {
				foreach ($fileInfos as $fileInfo) {
					$dirPath = $fileInfo->fullPath . '/';
					if (GalleryImage::model()->findByPk($fileInfo->id)->delete()) {
						if (@rmdir($dirPath)) {
							return true;
						}
					}
				}
			}
		}
		return true;
	}

	public function scopes()
	{

		return array(
			'onlyActive' => array('condition' => "statusId = '3'"),
		);
	}

	/**
	 * @deprecated as Not implemented
	 *
	 */
	public static function enabled()
	{

		return;
	}
}