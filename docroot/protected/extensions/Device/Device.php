<?php
include __DIR__ . '/MobileDetect.php';

class Device extends MobileDetect
{
	public $classic = 'classic';
	public $smallDevice = 'smallDevice';
	public $mobile = 'mobile';
	public $tablet = 'tablet';

	public function setDevice($type = null)
	{

		$device = new CHttpSession();
		$device->open();
		$device['device-type'] = $type ? $type : $this->classic;
		$device->close();
	}

	public function getDevice()
	{

		$device = new CHttpSession();
		$device->open();
		$type = $device['device-type'];

		return $type;
	}

	public function isDevice($type = null, $userAgent = null, $httpHeaders = null)
	{

		$type            = $type ? $type : $this->classic;
		$requestedDevice = $this->getDevice() ? ($this->getDevice() == $type) : true;

		switch ($type) {
			case $this->mobile :
				$isDevice = $this->isMobile($userAgent = null, $httpHeaders = null);
				break;
			case $this->tablet :
				$isDevice = $this->isTablet($userAgent = null, $httpHeaders = null);
				break;
			case $this->smallDevice :
				$isDevice = $this->isMobile($userAgent = null, $httpHeaders = null) || $this->isTablet($userAgent = null, $httpHeaders = null);
				break;
			default :
				$isDevice = true;
				break;
		}

//		$isDevice = true;
		return $isDevice && $requestedDevice;
	}
}