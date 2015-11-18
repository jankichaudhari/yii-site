<?php

/**
 * This is the model class for table "portal_ftp".
 *
 * The followings are the available columns in table 'portal_ftp':
 * @property string $portal_id
 * @property string $portal_name
 * @property string $ftp_server
 * @property string $ftp_username
 * @property string $ftp_password
 * @property string $ftp_dest_folder
 * @property string $filename
 */
class FeedPortal extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FeedPortal the static model class
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

		return 'portal_ftp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return array(
			array('ftp_server, ftp_username, ftp_password, portal_name', 'required'),
			array('ftp_dest_folder', 'default', 'value' => '/'),
			array('filename', 'type', 'type' => 'string'),
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
			'portal_id'       => 'Portal',
			'portal_name'     => 'Portal Name',
			'ftp_server'      => 'Ftp Server',
			'ftp_username'    => 'Ftp Username',
			'ftp_password'    => 'Ftp Password',
			'ftp_dest_folder' => 'Ftp Dest Folder',
			'filename'        => 'Filename',
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
												   'criteria'   => $criteria,
												   'pagination' => false,
											  ));
	}
}