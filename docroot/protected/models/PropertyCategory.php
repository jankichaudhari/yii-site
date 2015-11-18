<?php

/**
 * This is the model class for table "propertyCategory".
 *
 * The followings are the available columns in table 'propertyCategory':
 * @property integer  $id
 * @property string   $status
 * @property string   $created
 * @property string   $modified
 * @property string   $title
 * @property string   $description
 * @property string   $displayOnHome
 * @property string   $displayInMenu
 * @property string   $displayName
 * @property string   $bgColour
 * @property string   $textColour
 * @property string   $hoverBgColour
 * @property string   $hoverTextColour
 * @property integer  $matchClients
 *
 * @method PropertyCategory active()
 * @method PropertyCategory displayOnHome()
 * @method PropertyCategory matchClients()
 */
class PropertyCategory extends CActiveRecord
{
	const CATEGORY_PHOTO_PREFIX = 'PropertyCategory';
	public $propertyCategoryStatus = "propertyCategoryStatus";

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PropertyCategory the static model class
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

		return 'propertyCategory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
				['title, displayName ', 'length', 'max' => 255],
				['bgColour, textColour, hoverBgColour, hoverTextColour', 'length', 'max' => 255],
				['status', 'length', 'max' => 1],
				['displayOnHome, displayInMenu, matchClients', 'in', 'range' => [0, 1]],
				['description', 'safe'],
				array(
						'id, title, displayName, description, status, created, modified, displayOnHome, displayInMenu, bgColour, textColour, hoverBgColour, hoverTextColour',
						'safe',
						'on' => 'search'
				),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
				'statusValue' => [self::BELONGS_TO, "Lists", ["propertyCategoryStatus" => 'ListName', 'status' => 'ListItemID']],
		);
	}

	public function getName()
	{
		return $this->displayName ? : $this->title;
	}

	protected function beforeSave()
	{

		if ($this->isNewRecord) {
			$this->created = date("Y-m-d H:i:s");
		}

		$this->modified = date("Y-m-d H:i:s");
		return true;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
				'id'              => 'ID',
				'status'          => 'Status',
				'created'         => 'Created',
				'modified'        => 'Modified',
				'title'           => 'Name',
				'bgColour'        => 'Display text background colour #',
				'textColour'      => 'Display text colour #',
				'hoverBgColour'   => 'Rollover display text background colour #',
				'hoverTextColour' => 'Rollover display text colour #',
				'displayName'     => 'Display Name',
				'description'     => 'Description',
				'displayOnHome'   => 'Display On Home',
				'displayInMenu'   => 'Display In Menu',
				'matchClients'    => 'Match Clients',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		$criteria = new CDbCriteria;

		return new CActiveDataProvider($this, CMap::mergeArray(['criteria' => $criteria], Yii::app()->params['CActiveDataProvider']));
	}

	public function scopes()
	{

		return array(
				'active'        => ['condition' => "status = '1'"],
				'displayOnHome' => ['condition' => "displayOnHome = '1'"],
				'displayInMenu' => ['condition' => "displayInMenu = '1'"],
				'matchClients'  => ['condition' => "matchClients = 1"]
		);
	}

	/**
	 * @todo make static
	 * @return array
	 */
	public function getPhotoTypes()
	{

		return array(
				'_wide_top'      => 'Page wide top photo',
				'_text'          => 'Display text (PNG or GIF for transparency)',
				'_banner'        => 'Display text background',
				'_category_text' => 'Rollover display text (PNG or GIF for transparency)',
				'_category'      => 'Rollover display text background',
		);
	}

	/**
	 * @return string
	 */
	public function getImageFolderPath()
	{

		return '/images/propertyCategory/' . $this->id;
	}

	public function getImageURIPath($type = '')
	{

		if (!array_key_exists($type, $this->getPhotoTypes())) {
			return false;
		}
		$photo  = false;
		$photos = File::model()->findAllByAttributes([
															 'recordId'   => $this->id,
															 'recordType' => PropertyCategory::CATEGORY_PHOTO_PREFIX . $type
													 ]);
		foreach ($photos as $photo) {
			$photo = $this->getImageFolderPath() . "/" . $photo->name;
		}
		return $photo;
	}

}