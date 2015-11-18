<?php
/**
 * Class Redirect
 *
 * @property $id         int
 * @property $clientId   int
 * @property $url        string
 * @property $redirected string
 * @property $comment    string
 * @property $created    string
 */
class Redirect extends CActiveRecord
{
	public function tableName()
	{
		return 'redirect';
	}

	public function rules()
	{
		return [];
	}

	public function relations()
	{
		return [];
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function attributeLabels()
	{
		return array(
			'id'         => 'Id',
			'clientId'   => 'Client Id',
			'url'        => 'Url',
			'redirected' => 'Redirected',
			'comment'    => 'Comment',
			'created'    => 'Created'
		);
	}
}
