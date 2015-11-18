<?php

/**
 * This is the model class for table "branch".
 *
 * The followings are the available columns in table 'branch':
 * @property integer $bra_id
 * @property string  $bra_title
 * @property string  $bra_addr1
 * @property string  $bra_addr2
 * @property string  $bra_addr3
 * @property string  $bra_addr4
 * @property string  $bra_addr5
 * @property integer $bra_country
 * @property string  $bra_postcode
 * @property string  $bra_tel
 * @property string  $bra_fax
 * @property string  $bra_email
 * @property integer $bra_osx
 * @property integer $bra_osy
 * @property integer $bra_manager
 * @property string  $bra_image
 * @property string  $bra_blurb
 * @property string  $bra_status
 * @property string  $bra_colour
 *
 * @property Office  $office
 * @property Lists   $businessUnit
 *
 * @method Branch active() active scope
 * @method Branch registerClients()
 *
 * @see Branch::scopes()
 */
class Branch extends CActiveRecord
{

	const SALES       = 1;
	const LETTINGS    = 2;
	const MAINTENANCE = 3;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Branch the static model class
	 */
	public static function model($className = __CLASS__)
	{

		return parent::model($className);
	}

	public static function listData()
	{
		return CHtml::listData(self::model()->active()->findAll(), "bra_id", "bra_title");
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{

		return 'branch';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				['bra_title, bra_tel, bra_email', 'required'],
				['bra_colour', 'length', 'max' => 6],
				['bra_email', 'email'],
				['bra_status', 'in', 'range' => self::getStatuses()],
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array(
						'bra_id, bra_title, bra_addr1, bra_addr2, bra_addr3, bra_addr4, bra_addr5, bra_country, bra_postcode, bra_tel, bra_fax, bra_email, bra_osx, bra_osy, bra_manager, bra_image, bra_blurb, bra_status, bra_colour',
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

		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'office'       => array(self::BELONGS_TO, "Office", "office_id"),
				'businessUnit' => array(self::BELONGS_TO, "Lists", array("business_unit" => 'ListItemID'), 'on' => "businessUnit.ListName = 'BusinessUnit'"),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
				'bra_id'       => 'Bra',
				'bra_title'    => 'Title',
				'bra_addr1'    => 'Address line 1',
				'bra_addr2'    => 'Address line 2',
				'bra_addr3'    => 'Address line 3',
				'bra_addr4'    => 'Address line 4',
				'bra_addr5'    => 'Address line 5',
				'bra_country'  => 'Country',
				'bra_postcode' => 'Postcode',
				'bra_tel'      => 'Tel',
				'bra_fax'      => 'Fax',
				'bra_email'    => 'Email',
				'bra_osx'      => 'Osx',
				'bra_osy'      => 'Osy',
				'bra_manager'  => 'Manager',
				'bra_image'    => 'Image',
				'bra_blurb'    => 'Blurb',
				'bra_status'   => 'Status',
				'bra_colour'   => 'Colour',
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

		$criteria->compare('bra_id', $this->bra_id);
		$criteria->compare('bra_title', $this->bra_title, true);
		$criteria->compare('bra_addr1', $this->bra_addr1, true);
		$criteria->compare('bra_addr2', $this->bra_addr2, true);
		$criteria->compare('bra_addr3', $this->bra_addr3, true);
		$criteria->compare('bra_addr4', $this->bra_addr4, true);
		$criteria->compare('bra_addr5', $this->bra_addr5, true);
		$criteria->compare('bra_country', $this->bra_country);
		$criteria->compare('bra_postcode', $this->bra_postcode, true);
		$criteria->compare('bra_tel', $this->bra_tel, true);
		$criteria->compare('bra_fax', $this->bra_fax, true);
		$criteria->compare('bra_email', $this->bra_email, true);
		$criteria->compare('bra_osx', $this->bra_osx);
		$criteria->compare('bra_osy', $this->bra_osy);
		$criteria->compare('bra_manager', $this->bra_manager);
		$criteria->compare('bra_image', $this->bra_image, true);
		$criteria->compare('bra_blurb', $this->bra_blurb, true);
		$criteria->compare('bra_status', $this->bra_status, true);
		$criteria->compare('bra_colour', $this->bra_colour, true);

		return new CActiveDataProvider($this, array(
				'criteria' => $criteria,
		));
	}

	public function scopes()
	{

		return array(
				'active'          => array(
						'condition' => "bra_status = 'Active'",
				),
				'registerClients' => array(
						'condition' => 'business_unit = ' . self::SALES . ' AND bra_status = "Active"',
				)
		);
	}

	public function defaultScope()
	{

		return array(
				'order' => 'office_id',
				'with'  => array('office', 'businessUnit'),
				//			'condition' => "bra_status = 'Active'"
		);
	}

	public static function getStatuses()
	{

		return array(
				'Pending'  => 'Pending',
				'Active'   => 'Active',
				'Archived' => 'Archived',
				'Inactive' => 'Inactive',
		);
	}
}
