<?php

/**
 * This is the model class for table "feature".
 *
 * The followings are the available columns in table 'feature':
 * @property integer $fea_id
 * @property string  $fea_title
 * @property string  $fea_type
 * @property integer $fea_weight
 *
 * @method Feature notCustom()
 */
class Feature extends CActiveRecord
{

	const TYPE_EXTERNAL = 'External';
	const TYPE_INTERNAL = 'Internal';
	const TYPE_LOCALITY = 'Locality';
	const TYPE_LETTINGS = 'Lettings';
	const TYPE_CUSTOM   = 'Custom';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Feature the static model class
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
		return 'feature';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
				['fea_weight', 'required'],
				['fea_weight', 'numerical', 'integerOnly' => true],
				['fea_title', 'length', 'max' => 100],
				['fea_type', 'length', 'max' => 8],
				['fea_id, fea_title, fea_type, fea_weight', 'safe', 'on' => 'search'],
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
				'fea_id'     => 'Fea',
				'fea_title'  => 'Fea Title',
				'fea_type'   => 'Fea Type',
				'fea_weight' => 'Fea Weight',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('fea_id', $this->fea_id);
		$criteria->compare('fea_title', $this->fea_title, true);
		$criteria->compare('fea_type', $this->fea_type, true);
		$criteria->compare('fea_weight', $this->fea_weight);

		return new CActiveDataProvider($this, ['criteria' => $criteria]);
	}

	public function scopes()
	{
		return array(
				'notCustom' => ['condition' => "fea_type != '" . self::TYPE_CUSTOM . "'"]
		);
	}

	public function defaultScope()
	{
		return ['condition' => "fea_type != '" . self::TYPE_LETTINGS . "'"];
	}

	public function init()
	{
		if ($this->scenario === 'custom') {
			$this->fea_type   = self::TYPE_CUSTOM;
			$this->fea_weight = 0;
		}
		parent::init();
	}

}