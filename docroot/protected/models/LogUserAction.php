<?php

/**
 * This is the model class for table "logUserAction".
 *
 * The followings are the available columns in table 'logUserAction':
 * @property integer  $id
 * @property integer  $userId
 * @property string   $method
 * @property string   $get_data
 * @property string   $post_data
 * @property string   $session
 * @property string   $request
 * @property string   $controller
 * @property string   $action
 * @property string   $date
 * @property integer  $previousActionId
 * @property string   $ip
 * @property string   $referer
 */
class LogUserAction extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogUserAction the static model class
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

		return 'logUserAction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() // read only
	{

	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'         => 'ID',
			'userId'     => 'User',
			'method'     => 'Method',
			'get_data'   => 'Get Data',
			'post_data'  => 'Post Data',
			'session'    => 'Session',
			'request'    => 'Request',
			'controller' => 'Controller',
			'action'     => 'Action',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

		return new CActiveDataProvider($this);
	}
}