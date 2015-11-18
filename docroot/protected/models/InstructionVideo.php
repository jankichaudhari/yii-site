<?php

/**
 * This is the model class for table "instructionVideo".
 *
 * The followings are the available columns in table 'instructionVideo':
 * @property integer $id
 * @property integer $instructionId
 * @property string  $videoId
 * @property string  $host
 * @property string  $videoData
 * @property string  $featuredVideo
 * @property string  $displayOnSite
 *
 * @property Deal    $instruction
 */
class InstructionVideo extends CActiveRecord
{

	protected $videoDataObject;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return InstructionVideo the static model class
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

		return 'instructionVideo';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{

		return array(
			'instruction' => [self::BELONGS_TO, 'Deal', 'instructionId', 'together' => true]
		);
	}

	public function scopes()
	{

		return array(
			'publicAvailableInstruction' => array(
				'with'      => ['instruction'],
				'condition' => "instruction.dea_status IN ('" . Deal::STATUS_AVAILABLE . "','" . Deal::STATUS_UNDER_OFFER . "','" . Deal::STATUS_UNDER_OFFER_WITH_OTHER . "','" . Deal::STATUS_EXCHANGED . "') OR instruction.displayOnWebsite = 1",
			)
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('instructionId', 'numerical', 'integerOnly' => true),
			array('videoId, host', 'length', 'max' => 255),
			array('videoData,featuredVideo, displayOnSite', 'safe'),
			array('id, instructionId, videoId, host, videoData,featuredVideo', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
			'id'            => 'ID',
			'instructionId' => 'Instruction',
			'videoId'       => 'Video',
			'host'          => 'Host',
			'videoData'     => 'Video Data',
			'featuredVideo' => 'Featured Video',
		);
	}

	public function behaviors()
	{

		return array(
			'createdModifiedBehavior' => array(
				'class' => 'application.components.behaviours.CreatedModifiedBehavior',
			),
		);
	}

	public function getVideoData()
	{

		if (!$this->videoDataObject) {
			$this->videoDataObject = new VideoData(json_decode($this->videoData));
		}
		return $this->videoDataObject;
	}

	public static function copyRecords($from, $to)
	{

		$sql = "INSERT INTO instructionVideo (instructionId, videoId,host,videoData,featuredVideo, created, createdBy, modified, modifiedBy)
		SELECT :to, videoId,host,videoData,featuredVideo, :created, :createdBy, :modified, :modifiedBy FROM instructionVideo WHERE instructionId=:from";
		return Yii::app()->db->createCommand($sql)->execute(array(
																 ':to'         => $to,
																 ':from'       => $from,
																 ':created'    => date('Y-m-d H:i:s'),
																 ':createdBy'  => Yii::app()->user->id,
																 ':modified'   => date('Y-m-d H:i:s'),
																 ':modifiedBy' => Yii::app()->user->id,
															));

	}
}

/**
 * Class VideoData helper class to store information received from vimeo
 */
class VideoData
{
	public $id = '';
	public $title = '';
	public $description = '';
	public $url = '';
	public $ubload_date = '';
	public $stats_number_of_likes = '';
	public $stats_number_of_comments = '';
	public $duration = '';
	public $width = '';
	public $height = '';
	public $tags = '';
	public $embed_privacy = '';
	public $thumbnail_small = '';
	public $thumbnail_medium = '';
	public $thumbnail_large = '';
	public $user_name = '';
	public $user_portrait_small = '';
	public $user_portrait_medium = '';
	public $user_portrait_large = '';
	public $user_portrait_huge = '';

	public function __construct($data = array())
	{

		foreach ($data as $key => $value) {
			if (isset($this->$key)) {
				$this->$key = $value;
			}
		}
	}
}