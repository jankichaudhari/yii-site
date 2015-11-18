<?php
/**
 * Class MandrillEmail
 *
 * @property String $id
 * @property String $messageId
 * @property String $status
 * @property String $sent
 * @property String $opened
 * @property String $clientId
 * @property String $created
 * @property String $email
 * @property String $rejectMessage
 *
 */
class MandrillEmail extends CActiveRecord
{
	const STATUS_QUEUED   = 'queued';
	const STATUS_SENT     = 'sent';
	const STATUS_REJECTED = 'rejected';
	const STATUS_BOUNCED  = 'bounced';
	const STATUS_DELAYED  = 'delayed';
	const STATUS_OPEN     = 'open';
	const STATUS_SPAM     = 'spam';
	const STATUS_INVALID  = 'invalid';

	/**
	 * @param string $className
	 * @return static
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return [
			['email, status', 'safe', 'on' => 'search']
		];
	}

	public function relations()
	{
		return [
			'message' => [self::BELONGS_TO, 'MandrillMessage', 'messageId']
		];
	}

	public function tableName()
	{
		return 'mandrillEmail';
	}

	public function attributeLabels()
	{
		return array(
			'id'            => 'Id',
			'messageId'     => 'MessageId',
			'status'        => 'Status',
			'sent'          => 'Sent',
			'opened'        => 'Opened',
			'clientId'      => 'ClientId',
			'created'       => 'Created',
			'email'         => 'Email',
			'rejectMessage' => 'Reject Message',
		);
	}

	public function queue($queue, $messageId)
	{

		$sql      = [];
		$statuses = self::getStatuses();
		$params   = array(
			'date'      => date('Y-m-d H:i:s'),
			'clientId'  => 0,
			'opened'    => 0,
			'messageId' => $messageId,
		);
		foreach ($queue as $key => $email) {
			$status                         = $statuses[$email['status']];
			$rejectMessage                  = (isset($email['reject_reason']) ? $email['reject_reason'] : "");
			$sql[]                          = "(:emailId_{$key}, :messageId, :status_{$key}, :date, :opened, :clientId, :date, :email_{$key}, :rejectMessage_{$key})";
			$params["emailId_{$key}"]       = $email['_id'];
			$params["status_{$key}"]        = $status;
			$params["email_{$key}"]         = $email['email'];
			$params["rejectMessage_{$key}"] = $rejectMessage;
		}

		if ($sql) {
			$query = "INSERT INTO " . $this->tableName() . " (id, messageId, status, sent, opened, clientId, created, email, rejectMessage) VALUES " . implode(', ', $sql);
			return Yii::app()->db->createCommand($query)->execute($params);
		}
		return false;

	}

	public static function getStatuses()
	{
		return array_combine($t = [
			self::STATUS_QUEUED,
			self::STATUS_SENT,
			self::STATUS_REJECTED,
			self::STATUS_BOUNCED,
			self::STATUS_DELAYED,
			self::STATUS_OPEN,
			self::STATUS_SPAM,
		], $t);
	}

	public function search()
	{
		$criteria = $this->getDbCriteria();
		$criteria->compare('messageId', $this->messageId);
		$criteria->compare('email', $this->email, true);
		return new CActiveDataProvider($this, CMap::mergeArray(Yii::app()->params['CActiveDataProvider'], ['criteria' => $criteria]));
	}

}
