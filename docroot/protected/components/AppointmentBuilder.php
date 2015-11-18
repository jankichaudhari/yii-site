<?php

class AppointmentBuilder extends CFilter
{

	const TYPE_VALUATION = 'valuation';
	/**
	 * @var CHttpSession
	 */
	private static $session;

	private static $current = null;

	/**
	 * Static factory
	 */
	public static function start()
	{

		self::$current = new self();
		return self::$current;
	}

	/**
	 * static factory
	 */
	public static function getCurrent()
	{

		if (!self::$current) {
			return self::start();
		}

		return self::$current;
	}

	private $clientId;
	private $propertyId;
	private $instructionId;
	private $type;
	private $date;
	private $user;
	private $currentStep;

	public function __construct()
	{

	}

	protected function postFilter($filterChain)
	{

		self::$session->add('appointmentBuilder', self::$current);

		parent::postFilter($filterChain);
	}

	protected function preFilter($filterChain)
	{

		/** @var CHttpSession */
		self::$session = Yii::app()->session;
		if (self::$session->get('appointmentBuilder')) {
			self::$current = self::$session->get('appointmentBuilder');
		}
		return parent::preFilter($filterChain);
	}

	public function getClientId()
	{

		return $this->clientId;
	}

	public function setClientId($clientId)
	{

		$this->clientId = $clientId;
		return $this;
	}

	public function getPropertyId()
	{

		return $this->propertyId;
	}

	public function setPropertyId($propertyId)
	{

		$this->propertyId = $propertyId;
		return $this;
	}

	public function getInstructionId()
	{

		return $this->instructionId;
	}

	public function setInstructionId($instructionId)
	{

		$this->instructionId = $instructionId;
		return $this;
	}

	public function getCurrentStep()
	{

		return $this->currentStep;
	}

	public function setCurrentStep($currentStep)
	{

		$this->currentStep = $currentStep;
		return $this;
	}

	public function getType()
	{

		return $this->type;
	}

	public function setType($type)
	{

		$this->type = $type;
		return $this;
	}

	public function getDate()
	{

		return $this->date;
	}

	public function setDate($date)
	{

		$this->date = $date;
		return $this;
	}

	public function getUser()
	{

		return $this->user;
	}

	public function setUser($user)
	{

		$this->user = $user;
		return $this;
	}

}