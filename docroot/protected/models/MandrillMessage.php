<?php
/**
 * Class MandrillMessage
 *
 * @property int           $id
 * @property String        $text
 * @property String        $from
 * @property String        $subject
 * @property String        $message
 * @property String        $created
 * @property int           $createdBy
 * @property int           $type
 *
 * @property MandrillEmail $emails
 * @property int           $emailsCount
 * @property int           $emailsQueued
 * @property int           $emailsSent
 * @property int           $emailsRejected
 * @property int           $emailsBounced
 * @property int           $emailsOpened
 */
class MandrillMessage extends CActiveRecord
{

	CONST KEY_HTML                      = 'html';
	CONST KEY_TEXT                      = 'text';
	CONST KEY_SUBJECT                   = 'subject';
	CONST KEY_FROM_EMAIL                = 'from_email';
	CONST KEY_FROM_NAME                 = 'from_name';
	CONST KEY_TO                        = 'to';
	CONST KEY_HEADERS                   = 'headers';
	CONST KEY_IMPORTANT                 = 'important';
	CONST KEY_TRACK_OPENS               = 'track_opens';
	CONST KEY_TRACK_CLICKS              = 'track_clicks';
	CONST KEY_AUTO_TEXT                 = 'auto_text';
	CONST KEY_AUTO_HTML                 = 'auto_html';
	CONST KEY_INLINE_CSS                = 'inline_css';
	CONST KEY_URL_STRIP_QS              = 'url_strip_qs';
	CONST KEY_PRESERVE_RECIPIENTS       = 'preserve_recipients';
	CONST KEY_VIEW_CONTENT_LINK         = 'view_content_link';
	CONST KEY_BCC_ADDRESS               = 'bcc_address';
	CONST KEY_TRACKING_DOMAIN           = 'tracking_domain';
	CONST KEY_SIGNING_DOMAIN            = 'signing_domain';
	CONST KEY_RETURN_PATH_DOMAIN        = 'return_path_domain';
	CONST KEY_MERGE                     = 'merge';
	CONST KEY_GLOBAL_MERGE_VARS         = 'global_merge_vars';
	CONST KEY_MERGE_VARS                = 'merge_vars';
	CONST KEY_TAGS                      = 'tags';
	CONST KEY_SUBACCOUNT                = 'subaccount';
	CONST KEY_GOOGLE_ANALYTICS_DOMAINS  = 'google_analytics_domains';
	CONST KEY_GOOGLE_ANALYTICS_CAMPAIGN = 'google_analytics_campaign';
	CONST KEY_METADATA                  = 'metadata';
	CONST KEY_RECIPIENT_METADATA        = 'recipient_metadata';
	CONST KEY_ATTACHMENTS               = 'attachments';
	CONST KEY_IMAGES                    = 'images';

	/**
	 * @var array
	 */
	private $messageBaseTemplate = array(
		'html'                      => '',
		'text'                      => '',
		'subject'                   => '',
		'from_email'                => null,
		'from_name'                 => '',
		'to'                        => [],
		'headers'                   => [],
		'important'                 => false,
		'track_opens'               => null,
		'track_clicks'              => null,
		'auto_text'                 => null,
		'auto_html'                 => null,
		'inline_css'                => null,
		'url_strip_qs'              => null,
		'preserve_recipients'       => false,
		'view_content_link'         => null,
		'bcc_address'               => null,
		'tracking_domain'           => null,
		'signing_domain'            => null,
		'return_path_domain'        => null,
		'merge'                     => null,
		'global_merge_vars'         => [],
		'merge_vars'                => [],
		'tags'                      => [],
		'subaccount'                => null,
		'google_analytics_domains'  => [],
		'google_analytics_campaign' => null,
		'metadata'                  => [],
		'recipient_metadata'        => [],
		'attachments'               => [],
		'images'                    => []
	);

	/**
	 * @var array
	 */
	private $messageTemplate = [];

	/**
	 * @var array
	 */
	private $mergeVars = [];

	private $recepients = [];

	private $globalMergeVars = [];
	private $messageMetaData = [];

	private $testRun = false;

	/**
	 * @param string $className
	 * @return static
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return [
			'emails'         => [self::HAS_MANY, 'MandrillEmail', 'messageId'],
			'emailsCount'    => [self::STAT, 'MandrillEmail', 'messageId'],

			'emailsQueued'   => [self::STAT, 'MandrillEmail', 'messageId', 'condition' => 'status = "' . MandrillEmail::STATUS_QUEUED . '"'],
			'emailsSent'     => [self::STAT, 'MandrillEmail', 'messageId', 'condition' => 'status = "' . MandrillEmail::STATUS_SENT . '"'],
			'emailsRejected' => [self::STAT, 'MandrillEmail', 'messageId', 'condition' => 'status = "' . MandrillEmail::STATUS_REJECTED . '"'],
			'emailsBounced'  => [self::STAT, 'MandrillEmail', 'messageId', 'condition' => 'status = "' . MandrillEmail::STATUS_BOUNCED . '"'],
			'emailsOpened'   => [self::STAT, 'MandrillEmail', 'messageId', 'condition' => 'status = "' . MandrillEmail::STATUS_OPEN . '"'],
		];
	}

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'id'        => 'Id',
			'text'      => 'Text',
			'from'      => 'From Address',
			'subject'   => 'Subject',
			'message'   => 'Message Data',
			'created'   => 'Created',
			'createdBy' => 'Created By',
			'type'      => 'Type',
		);
	}

	public function enableTest()
	{
		$this->testRun = true;
	}

	public function disableTest()
	{
		$this->testRun = false;
	}

	/**
	 *
	 */
	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->created   = date('Y-m-d H:i:s');
			$this->createdBy = isset(Yii::app()->user->id) ? Yii::app()->user->id : 0;
		}
		return parent::beforeSave();
	}

	/**
	 * @return mixed
	 */
	public function send()
	{

		if (!$this->getOption(self::KEY_FROM_EMAIL)) {
			throw new Exception('cannot send message if Sender is not specified');
		}

		$apikey = Yii::app()->params['mandrill']['API_KEY'];

		if ($this->testRun) {
			$apikey = Yii::app()->params['mandrill']['TEST_API_KEY'];
		}

		$mandrill      = new Mandrill($apikey);
		$this->message = $this->getMessage();
		$this->subject = $this->getOption(self::KEY_SUBJECT);
		$this->from    = $this->getOption(self::KEY_FROM_EMAIL);
		$this->text    = $this->getOption(self::KEY_HTML);
		$result        = $mandrill->messages->send($this->message);
		$this->message = serialize($this->message);
		$r             = $this->save(false);
		MandrillEmail::model()->queue($result, $this->id);
		return $r;
	}

	public function setSubject($subject)
	{
		$this->setOption(self::KEY_SUBJECT, $subject);
	}

	public function addTo($email, $name = '')
	{
		if (!$email) {
			throw new  InvalidArgumentException('email must be a non empty string');
		}

		$this->recepients[$email] = ['email' => $email, 'name' => $name];
	}

	public function getRecepients($preserveMailKeys = true)
	{
		if ($preserveMailKeys) {
			return $this->recepients;
		} else {
			return array_values($this->recepients);
		}
	}

	public function getMessage()
	{
		$message                       = CMap::mergeArray($this->messageBaseTemplate, $this->messageTemplate);
		$message[self::KEY_MERGE_VARS] = [];
		foreach ($this->mergeVars as $recepient => $vars) {
			$message[self::KEY_MERGE_VARS][] = ['rcpt' => $recepient, 'vars' => $vars];
		}
		foreach ($this->messageMetaData as $recepient => $meta) {
			$message[self::KEY_RECIPIENT_METADATA][] = ['rcpt' => $recepient, 'values' => $meta];
		}
		$message[self::KEY_GLOBAL_MERGE_VARS] = array_values($this->globalMergeVars);
		$message[self::KEY_TO]                = array_values($this->recepients);
		return $message;
	}

	/**
	 * @param $option
	 * @param $value
	 * @throws InvalidArgumentException
	 */
	public function setOption($option, $value)
	{
		if (!in_array($option, array_keys($this->messageBaseTemplate))) {
			throw new InvalidArgumentException($option . ' is not a valid option name');
		}

		if ($option === self::KEY_MERGE_VARS && is_array($value)) {
			foreach ($value as $mergeVar) {
				foreach ($mergeVar['vars'] as $var) {
					$this->setRecepientMergeVar($mergeVar['rcpt'], $var['name'], $var['content']);

				}
			}
		} elseif ($option === self::KEY_GLOBAL_MERGE_VARS && is_array($value)) {
			foreach ($value as $mergeVar) {
				$this->setGlobalMergeVar($mergeVar['name'], $mergeVar['content']);
			}

		}
		if ($option === self::KEY_RECIPIENT_METADATA && is_array($value)) {
			foreach ($value as $metaData) {
				foreach ($metaData['values'] as $name => $val) {
					$this->setRecepientMetaData($metaData['rcpt'], $name, $val);

				}
			}
		} else {
			$this->messageTemplate[$option] = $value;
		}
	}

	public function getOption($option)
	{
		if (!in_array($option, array_keys($this->messageBaseTemplate))) {
			throw new InvalidArgumentException($option . ' is not a valid option name');
		}

		$message = $this->getMessage();
		return $message[$option];
	}

	/**
	 * @param $recepient String email address
	 * @param $var       String variable name
	 * @param $value     String variable value
	 */
	public function setRecepientMergeVar($recepient, $var, $value)
	{
		if (!isset($this->mergeVars[$recepient])) {
			$this->mergeVars[$recepient] = [['name' => $var, 'content' => $value]];
		} else {
			$this->mergeVars[$recepient][] = ['name' => $var, 'content' => $value];
		}
	}

	public function setRecepientMetaData($recepient, $metaName, $metaValue)
	{
		if (!isset($this->messageMetaData[$recepient])) {
			$this->messageMetaData[$recepient] = [$metaName => $metaValue];
		} else {
			$this->messageMetaData[$recepient][$metaName] = $metaValue;
		}
	}

	public function setGlobalMergeVar($var, $value)
	{
		$this->globalMergeVars[$var] = ['name' => $var, 'content' => $value];
	}

	public function getMergeVars()
	{
		return $this->mergeVars;
	}

	/**
	 * @return string
	 */
	public function tableName()
	{
		return 'mandrillMessage';
	}

	/**
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = $this->getDbCriteria();

		return new CActiveDataProvider($this, CMap::mergeArray(Yii::app()->params['CActiveDataProvider'], []));
	}

	public function addTag($tag)
	{
		$tags = $this->getOption(self::KEY_TAGS);
		if (!in_array($tag, $tags)) {
			$tags[] = $tag;
			$this->setOption(self::KEY_TAGS, $tags);
		}
	}

	public function trackOpenEnable()
	{
		$this->setOption(self::KEY_TRACK_OPENS, true);
	}

	public function trackOpenDisable()
	{
		$this->setOption(self::KEY_TRACK_OPENS, false);
	}

	public function trackClicksEnable()
	{
		$this->setOption(self::KEY_TRACK_CLICKS, true);
	}

	public function trackClicksDisable()
	{
		$this->setOption(self::KEY_TRACK_CLICKS, false);
	}

	public function setFrom($email, $name = '')
	{

		$this->setOption(self::KEY_FROM_EMAIL, $email);
		if ($name) {
			$this->setOption(self::KEY_FROM_NAME, $name);
		}
	}

	public function setTextBody($text)
	{
		$this->setOption(self::KEY_TEXT, $text);
	}

	public function setHtmlBody($string)
	{
		$this->setOption(self::KEY_HTML, $string);
	}

	public function setImportant($important)
	{
		$this->setOption(self::KEY_IMPORTANT, $important);
	}

	public function setPreserveRecepients($value)
	{
		$this->setOption(self::KEY_PRESERVE_RECIPIENTS, $value);
	}

	public function setBcc($email)
	{
		$this->setOption(self::KEY_BCC_ADDRESS, $email);
	}

}
