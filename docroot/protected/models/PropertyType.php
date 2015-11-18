<?php

/**
 * This is the model class for table "ptype".
 *
 * The followings are the available columns in table 'ptype':
 * @property integer $pty_id
 * @property string  $pty_title
 * @property integer $pty_type
 */
class PropertyType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PropertyType the static model class
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
		return 'ptype';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
				array('pty_type', 'numerical', 'integerOnly' => true),
				array('pty_title', 'length', 'max' => 255),
				array('pty_id, pty_title, pty_type', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'pty_id'    => 'Pty',
				'pty_title' => 'Pty Title',
				'pty_type'  => 'Pty Type',
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

	public function getTypes($parent = null)
	{
		$crtieria = new CDbCriteria();
		if ($parent) {
			$crtieria->compare('pty_type', $parent);
		} else {
			$crtieria->addCondition('pty_type is NULL');
		}
		return $this->findAll($crtieria);
	}

	public function getPublicSiteTypes($type = 'sales')
	{
		$type = strtolower($type);
		$type = in_array($type, ['sales', 'lettings']) ? $type : 'sales';

		$mainTypes = $this->getTypes(0);

		if ($type == 'sales') {
			foreach ($mainTypes as $key => $value) { // that may look strange, but we try not to rely on ids. so ve go through record until we find Other;
				if (strtolower($value['pty_title']) == 'other') {
					$mainTypes[$key]['pty_title'] = 'Other/Commercial/Mixed-Use';
					break;
				}
			}
		}

		return $mainTypes;
	}

}