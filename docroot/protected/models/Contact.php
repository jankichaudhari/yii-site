<?php

/**
 * This is the model class for table "contact".
 *
 * The followings are the available columns in table 'contact':
 * @property integer $con_id
 * @property string  $con_status
 * @property string  $con_created
 * @property string  $con_salutation
 * @property string  $con_fname
 * @property string  $con_sname
 * @property integer $con_type
 * @property string  $con_company
 * @property integer $con_pro
 * @property string  $con_email
 * @property string  $con_web
 * @property string  $con_password
 * @property string  $con_question
 * @property string  $con_answer
 * @property string  $con_timestamp
 */
class Contact extends CActiveRecord
{
	Public $fullName = "";

	protected function afterFind()
	{

		$this->fullName = trim($this->con_fname . " " . $this->con_sname);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Contact the static model class
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

		return 'contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('con_timestamp', 'required'),
			array('con_type, con_pro', 'numerical', 'integerOnly' => true),
			array('con_status', 'length', 'max' => 8),
			array('con_salutation', 'length', 'max' => 5),
			array('con_fname, con_sname, con_answer', 'length', 'max' => 100),
			array('con_company, con_email, con_web', 'length', 'max' => 255),
			array('con_password', 'length', 'max' => 30),
			array('con_question', 'length', 'max' => 19),
			array('con_created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'con_id, con_status, con_created, con_salutation, con_fname, con_sname, con_type, con_company, con_pro, con_email, con_web, con_password, con_question, con_answer, con_timestamp',
				'safe', 'on' => 'search'
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
		return array();
	}

	public function scopes()
	{

		return array(
			'active'         => array('condition' => "con_status = 'Active'"),
			'alphabetically' => array('order' => 'con_fname ASC')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'con_id'         => 'Con',
			'con_status'     => 'Con Status',
			'con_created'    => 'Con Created',
			'con_salutation' => 'Con Salutation',
			'con_fname'      => 'Con Fname',
			'con_sname'      => 'Con Sname',
			'con_type'       => 'Con Type',
			'con_company'    => 'Con Company',
			'con_pro'        => 'Con Pro',
			'con_email'      => 'Con Email',
			'con_web'        => 'Con Web',
			'con_password'   => 'Con Password',
			'con_question'   => 'Con Question',
			'con_answer'     => 'Con Answer',
			'con_timestamp'  => 'Con Timestamp',
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

		$criteria->compare('con_id', $this->con_id);
		$criteria->compare('con_status', $this->con_status, true);
		$criteria->compare('con_created', $this->con_created, true);
		$criteria->compare('con_salutation', $this->con_salutation, true);
		$criteria->compare('con_fname', $this->con_fname, true);
		$criteria->compare('con_sname', $this->con_sname, true);
		$criteria->compare('con_type', $this->con_type);
		$criteria->compare('con_company', $this->con_company, true);
		$criteria->compare('con_pro', $this->con_pro);
		$criteria->compare('con_email', $this->con_email, true);
		$criteria->compare('con_web', $this->con_web, true);
		$criteria->compare('con_password', $this->con_password, true);
		$criteria->compare('con_question', $this->con_question, true);
		$criteria->compare('con_answer', $this->con_answer, true);
		$criteria->compare('con_timestamp', $this->con_timestamp, true);

		return new CActiveDataProvider($this, array(
												   'criteria' => $criteria,
											  ));
	}
}