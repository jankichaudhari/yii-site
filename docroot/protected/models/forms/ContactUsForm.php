<?php

class ContactUsForm extends CFormModel
{
	public $name;
	public $email;
	public $message;
	public $to;
	public $verifyCode;
	public $telephone;

	public function rules()
	{
		return array(
			['name, email, message, to', 'required', 'message' => '{attribute} cannot be blank'],
			['email', 'email', 'message' => '{attribute} is not valid'],
			['telephone', 'type'], // defaults to string
			['verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements(), 'message' => 'The verification code is incorrect'],
		);
	}

	public function attributeLabels()
	{
		return array(
			'name'       => 'Name',
			'email'      => 'Email',
			'message'    => 'Message',
			'to'         => 'To',
			'telephone'  => 'Phone number',
			'verifyCode' => 'Verification code'
		);
	}
}
