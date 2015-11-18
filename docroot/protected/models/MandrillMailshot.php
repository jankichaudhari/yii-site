<?php
/**
 * Class MandrillMailshot
 *
 * @property Int                 $id
 * @property string              $instructionId
 * @property string              $type
 * @property string              $created
 * @property string              $createdBy
 *
 * @property User                $creator
 * @property MandrillMailshotHit $hits
 * @property int                 $hitCount
 * @property int                 $uniqueHitCount
 *
 */
class MandrillMailshot extends CActiveRecord
{
	const LINK_TO_MESSAGE = 'link_mandrillMailshot_to_mandrillMessage';
	/**
	 * @var MandrillMessage[]
	 */
	private $queue = [];

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return [];
	}

	public function relations()
	{
		return array(
			'creator'          => [self::BELONGS_TO, 'User', 'createdBy'],
			'hits'             => [self::HAS_MANY, 'MandrillMailshotHit', 'mailshotId'],
			'hitCount'         => [self::STAT, 'MandrillMailshotHit', 'mailshotId'],
			'uniqueHitCount'   => [self::STAT, 'MandrillMailshotHit', 'mailshotId', 'select' => 'count(distinct t.clientId)'],
			'messages'         => [self::MANY_MANY, 'MandrillMessage', 'link_mandrillMailshot_to_mandrillMessage(mailshotId, messageId)'],
			'emailCount'       => array(
				self::STAT, 'MandrillMessage', 'link_mandrillMailshot_to_mandrillMessage(mailshotId, messageId)',
				'select' => 'count(t2.id)',
				'join'   => 'LEFT JOIN mandrillEmail t2 on t.id = t2.messageId'
			),
			'queuedEmailCount' => array(
				self::STAT, 'MandrillMessage', 'link_mandrillMailshot_to_mandrillMessage(mailshotId, messageId)',
				'select' => 'count(t2.id)',
				'join'   => 'LEFT JOIN mandrillEmail t2 on t.id = t2.messageId AND t2.status = "' . MandrillEmail::STATUS_QUEUED . '"'
			),
			'sentEmailCount'   => array(
				self::STAT, 'MandrillMessage', 'link_mandrillMailshot_to_mandrillMessage(mailshotId, messageId)',
				'select' => 'count(t2.id)',
				'join'   => 'LEFT JOIN mandrillEmail t2 on t.id = t2.messageId AND t2.status IN("' . MandrillEmail::STATUS_SENT . '", "' . MandrillEmail::STATUS_OPEN . '")'
			),
			'openEmailCount'   => array(
				self::STAT, 'MandrillMessage', 'link_mandrillMailshot_to_mandrillMessage(mailshotId, messageId)',
				'select' => 'count(t2.id)',
				'join'   => 'LEFT JOIN mandrillEmail t2 on t.id = t2.messageId AND t2.status = "' . MandrillEmail::STATUS_OPEN . '"'
			),

		);
	}

	public function tableName()
	{
		return 'mandrillMailshot';
	}

	public function attributeLabels()
	{
		return array();
	}

	public function addMessage(MandrillMessage $message)
	{

		if ($this->isNewRecord) {
			throw new LogicException('Cannot add message to non existing mailshot');
		}
		$sql = "REPLACE INTO link_mandrillMailshot_to_mandrillMessage SET mailshotId = '" . $this->id . "', messageId = '" . $message->id . "'";
		return Yii::app()->db->createCommand($sql)->execute();
	}

	public function queueMessage(MandrillMessage $message)
	{

		if (!in_array($message, $this->queue)) {
			$this->queue[] = $message;
		}
	}

	public function addQueuedMessages()
	{
		if ($this->isNewRecord) {
			throw new LogicException('Cannot add message to non existing mailshot');
		}

		$sql = [];
		foreach ($this->queue as $message) {
			if ($message->isNewRe) {
				$sql[] = '(' . $this->id . ', ' . $message->id . ')';
			}
		}
		if ($sql) {
			$sql = 'INSERT INTO ' . self::LINK_TO_MESSAGE . '(mailshotId, messageId) VALUES ' . implode(', ', $sql);
			Yii::app()->db->createCommand($sql)->execute();
		}

		$this->clearQueue();

	}

	public function clearQueue()
	{
		$this->queue = [];
		return $this;
	}

	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->createdBy = Yii::app()->user->id;
			$this->created   = date('Y-m-d H:i:s');
		}
		return parent::beforeSave();
	}

}
