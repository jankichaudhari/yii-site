<?php

class PublicCareerForm extends CFormModel
{

	public $email = '';
	public $name = '';
	public $telephone = '';
	public $message = '';
	public $cv = '';


	public function rules(){
		return array(
			array('email, name, telephone', 'required'),
			array('email', 'email'),
			array('message', 'safe'),
			array('cv', 'file', 'types' => 'doc, docx, pdf, txt, rtf')
		);
	}

	protected function beforeValidate()
	{
		$this->message = strip_tags($this->message);
		$this->name = strip_tags($this->name);
		$this->telephone = strip_tags($this->telephone);
		return parent::beforeValidate();
	}

}
