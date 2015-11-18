<?php
/**
 * Class MailshotType
 * @property String    $name
 * @property String    $subject
 * @property String    $htmlTemplate
 * @property String    $textTemplate
 * @property String    $description
 * @property String    $created
 * @property Int       $createdBy
 * @property String    $templatePath
 *
 * @property User      $creator
 */
class MailshotType extends CActiveRecord
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return [
			['name, subject', 'required'],
			['htmlTemplate, textTemplate, description, templatePath', 'type', 'type' => 'string'],
		];
	}

	public function relations()
	{
		return [
			'creator' => [self::BELONGS_TO, 'User', 'createdBy']
		];
	}

	public function tableName()
	{
		return 'mailshotType';
	}

	public function attributeLabels()
	{
		return array();
	}

	public function search()
	{
		return new CActiveDataProvider($this);
	}

	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->created   = date('Y-m-d H:i:s');
			$this->createdBy = Yii::app()->user->id;
		}

		if ($this->templatePath && $this->htmlTemplate) {
			file_put_contents($this->templatePath, $this->htmlTemplate);
		}
		return parent::beforeSave();
	}

	protected function afterFind()
	{
		if ($this->templatePath && file_exists($this->templatePath)) {
			$this->htmlTemplate = file_get_contents($this->templatePath);
		}
		parent::afterFind();
	}

}
