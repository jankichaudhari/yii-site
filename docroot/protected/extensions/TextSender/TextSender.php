<?php

class TextSender
{
	private $fromPhoneNumber;
	private $sid;
	private $accessToken;

	/**
	 * @return mixed
	 */
	public function getFromPhoneNumber()
	{
		return $this->fromPhoneNumber;
	}

	/**
	 * @param mixed $fromPhoneNumber
	 * @return $this
	 */
	public function setFromPhoneNumber($fromPhoneNumber)
	{
		$this->fromPhoneNumber = $fromPhoneNumber;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSid()
	{
		return $this->sid;
	}

	/**
	 * @param mixed $sid
	 * @return $this
	 */
	public function setSid($sid)
	{
		$this->sid = $sid;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAccessToken()
	{
		return $this->accessToken;
	}

	/**
	 * @param mixed $accessToken
	 * @return $this
	 */
	public function setAccessToken($accessToken)
	{
		$this->accessToken = $accessToken;
		return $this;
	}

}
