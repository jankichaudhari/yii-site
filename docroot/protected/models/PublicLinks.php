<?php

/**
 * This is the model class for table "public_links".
 *
 * The followings are the available columns in table 'public_links':
 * @property string $id
 * @property string $order_num
 * @property string $title
 * @property string $description
 * @property string $link
 * @property string $image
 */
class PublicLinks extends CActiveRecord
{
	private $imagePath;

	public function __construct($scenario = 'insert')
	{
		$this->imagePath = Yii::app()->params['imgPath'] . "/public_links";
		return parent::__construct($scenario);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PublicLinks the static model class
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
		return 'public_links';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('image', 'file',
				  'types'     => 'jpg, gif, png',
				  'allowEmpty'=> true),
			array('title, link', 'required'),
			array('link', 'url'),
			array('title', 'length',
				  'max'=> 255),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, order_num, title, description, link', 'safe',
				  'on'=> 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'          => 'ID',
			'order_num'   => 'Order',
			'title'       => 'Title',
			'description' => 'Description',
			'link'        => 'Link',
			'image'       => 'Image',
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

		$criteria->compare('id', $this->id, true);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('link', $this->link, true);

		return new CActiveDataProvider($this, array(
												   'criteria'=> $criteria,
											  ));
	}

	protected function beforeSave()
	{
		if (!$this->order_num) {
			$this->order_num = Yii::app()->db->createCommand("SELECT MAX(order_num) FROM " . $this->tableName() . "")->queryScalar() + 1;
		}
		return parent::beforeSave();

	}

	protected function afterSave()
	{
		parent::afterSave();

		if (!file_exists($this->imagePath)) {
			Yii::app()->file->set($this->imagePath)->createDir(0777);
		}
		$linkPath = $this->getLinkImagePath();
		if (!file_exists($linkPath)) {
			Yii::app()->file->set($linkPath)->createDir(0777);
		}

	}

	public function getLinkImagePath()
	{

		if ($this->id === null) {
			throw new  Exception("Can not get path for not existing link");
		} else return $this->imagePath . "/" . $this->id;
	}

}