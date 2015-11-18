<?php

/**
 * Class Blog
 * @property String      $id
 * @property String      $title
 * @property String      $body
 * @property String      $created
 * @property String      $createdBy
 * @property String      $status
 * @property String      $featuredImage
 * @property String      $deleted
 * @property String      $strapline
 *
 * @property User        $creator
 * @property BlogImage   $featuredImageModel well I fucked up. perhaps attribute should have been called featuredImageId but I am a bloody idiot so it's not;
 *
 * @method static published()
 */
class Blog extends CActiveRecord
{
	const STATUS_DRAFT     = 'Draft';
	const STATUS_PUBLISHED = 'Published';
	const STATUS_ARCHIVED  = 'Archived';
	const DELETED          = 1;
	const NOT_DELETED      = 0;

	public $status = '';

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return array(
				['status', 'in', 'range' => self::getStatuses()],
				['title, body, strapline', 'type', 'type' => 'string'],
				['featuredImage', 'numerical', 'integerOnly' => true],
				['title, created, createdBy, status, deleted', 'safe', 'on' => 'search'],

		);
	}

	public function relations()
	{
		return array(
				'creator'            => [self::BELONGS_TO, 'User', 'createdBy'],
				'featuredImageModel' => [self::BELONGS_TO, 'BlogImage', 'featuredImage'],
		);
	}

	public function tableName()
	{
		return 'blog';
	}

	public function attributeLabels()
	{
		return array();
	}

	public function search()
	{
		$criteria = $this->getDbCriteria();

		$criteria->with['creator'] = ['together' => true];
		$criteria->compare('status', $this->status);

		return new CActiveDataProvider($this, CMap::mergeArray(Yii::app()->params['CActiveDataProvider'], array(
				'criteria'   => $criteria,
				'pagination' => ['pageSize' => 20],
				'sort'       => array(
						'defaultOrder' => 'created DESC',
						'attributes'   => array(
								'creator.fullName' => array(
										'desc' => 'creator.use_sname DESC, creator.use_fname DESC',
										'asc'  => 'creator.use_sname ASC, creator.use_fname ASC'
								),
								'*'
						)
				),

		)));

	}

	public static function getStatuses()
	{
		return array_combine($t = [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_ARCHIVED], $t);
	}

	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->created   = date('Y-m-d H:i:s');
			$this->createdBy = Yii::app()->user->id;
		}
		return parent::beforeSave();
	}

	public function scopes()
	{
		return array(
				'published' => ['condition' => "status = '" . self::STATUS_PUBLISHED . "'"],
		);
	}

}
