<?php

/**
 * This is the model class for table "office".
 *
 * The followings are the available columns in table 'office':
 * @property string                                 $id
 * @property string                                 $title
 * @property string                                 $shortTitle
 * @property string                                 $description
 * @property string                                 $address1
 * @property string                                 $address2
 * @property string                                 $address3
 * @property string                                 $address4
 * @property string                                 $postcode
 * @property string                                 $email
 * @property string                                 $image
 * @property string                                 $addressId
 * @property Address                                $address
 * @property Branch[]                               $branches
 * @property LinkOfficeToPostcode[]                 $areas
 * @property String                                 $backgroundImage
 * @property integer                                $active
 * @property string                                 $phone
 *
 * @method Office enabledClientMatching()
 * @method Office active()
 */
final class Office extends CActiveRecord implements IHasAddress
{
	private $newPostcodes;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Office the static model class
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

		return 'office';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('title, shortTitle', 'required'),
			array('email', 'email'),
			array('title, shortTitle, email', 'length', 'max' => 255),
			array('code', 'length', 'max' => 5),
			array('clientMatching, active', 'in', 'range' => [0, 1]),
			array('phone', 'length', 'max' => 25),
			array('id, title, shortTitle, description, address1, address2, address3, address4, postcode, email, image', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			"branches" => array(self::HAS_MANY, "Branch", "office_id", 'scopes' => ['active']),
			"address"  => array(self::BELONGS_TO, "Address", "addressId"),
			'areas'    => [self::HAS_MANY, 'LinkOfficeToPostcode', 'officeId'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'             => 'ID',
			'title'          => 'Title',
			'shortTitle'     => 'Short Title',
			'description'    => 'Description',
			'address1'       => 'Address1',
			'address2'       => 'Address2',
			'address3'       => 'Address3',
			'address4'       => 'Address4',
			'postcode'       => 'Postcode',
			'email'          => 'Email',
			'image'          => 'Image',
			'active'         => 'Active',
			'clientMatching' => 'Client Matching'
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

	/**
	 *
	 *
	 * @param string $type
	 * @return array
	 */
	public function getShortBranchList($type = 'sales')
	{

		$sql      = "SELECT office.shortTitle, branch.bra_id, office.id FROM office INNER JOIN branch ON branch.office_id = office.id WHERE branch.business_unit = :businessUnit";
		$command  = Yii::app()->db->createCommand($sql);
		$queryAll = $command->queryAll(true, ['businessUnit' => ($type == 'sales' ? 1 : 2)]);

		$result = array();
		$cnt    = 0;
		foreach ($queryAll as $queryEach) {
			if (($queryEach['shortTitle'] == 'Brixton') && ($type == 'lettings')) {
				$sql2                = "SELECT bra_id FROM branch WHERE business_unit = 1 AND office_id = " . $queryEach['id'];
				$command2            = Yii::app()->db->createCommand($sql2);
				$queryAll2           = $command2->queryAll();
				$queryEach['bra_id'] = $queryAll2[0]['bra_id'];
			}
			$result[$cnt++] = $queryEach;
		}
		return $result;
	}

	/**
	 * Sets a list of new postcodes to be saved;
	 * they will completely replace all currently related postcodes to this office.
	 *
	 * All postcodes are transformed to uppercase.
	 *
	 * Duplicates are ignored(removed).
	 *
	 * @param $postcodes String[] just array of postcodes, keys do not matter.
	 */
	public function setPostcodes($postcodes)
	{

		$this->newPostcodes = array_unique(array_map("strtoupper", array_filter($postcodes)));
	}

	protected function afterSave()
	{

		$tableName = LinkOfficeToPostcode::model()->tableName();
		$sql       = 'DELETE FROM ' . $tableName . ' WHERE officeId = ' . $this->id . '';
		Yii::app()->db->createCommand($sql)->execute();

		if (!$this->newPostcodes) {
			return parent::afterSave();
		}

		$sql = [];
		foreach ($this->newPostcodes as $value) {
			$sql[] = '(' . $this->id . ', "' . $value . '")';
		}

		$sql = 'REPLACE INTO ' . $tableName . ' (officeId, postcode) VALUES ' . implode(', ', $sql);

		Yii::app()->db->createCommand($sql)->execute();
		parent::afterSave();
		$this->refresh();
	}

	public function scopes()
	{

		return array(
			'enabledClientMatching' => ['condition' => 'clientMatching = 1'],
			'active'                => ['condition' => 'active  = 1'],
		);
	}

	/**
	 * returns a reference to the actual IAdress Object
	 *
	 * helper method that in most cases will return $this. in case of property may return related address object in future.
	 *
	 * @return IAddress returns a reference to the actual IAdress Object
	 */
	public function getAddressObject()
	{

		return $this->address ? $this->address : new Address();
	}

	public function hasBranch($businessUnit)
	{

		foreach ($this->branches as $value) {
			if ($value->businessUnit && $value->businessUnit->ListItemID == $businessUnit) {
				return true;
			}
		}
		return false;
	}

	public function getBranch($businessUnit)
	{

		foreach ($this->branches as $key => $value) {
			if ($value->businessUnit->ListItemID == $businessUnit) {
				return $value;
			}
		}
	}
}