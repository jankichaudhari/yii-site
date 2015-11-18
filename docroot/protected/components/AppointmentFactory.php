<?php
/**
 * User: vitaly.suhanov
 * Date: 21/02/12
 * Time: 13:19
 */

class AppointmentFactory
{
	private static $_types = array('Viewing', 'Valuation', 'Production', 'Inspection', 'Meeting', 'Note', 'Lunch');

	/**
	 * @static
	 * @param $type
	 * @param string $scenario
	 * @return Appointment
	 */
	public static function getModel($type, $scenario = 'insert')
	{
		if (self::typeExists($type)) {
			return $type::model($scenario);
		}
	}

	/**
	 * @static
	 * @param $type
	 * @param string $scenarion
	 * @return Appointment
	 */
	public static function newInstance($type, $scenarion = 'insert')
	{
		if (self::typeExists($type)) {
			/** @var $instance Appointment */
			$instance = new $type($scenarion);
			return $instance;
		}
	}

	private static function typeExists($type)
	{
		if (!in_array($type, self::$_types)) {
			throw new AppointmentFactoryException("type [{$type}] is not defined in AppointmentFactory");
		} elseif (!class_exists($type)) {
			throw new AppointmentFactoryException("Class for appointment type [{$type}] does not exists");
		}
		return true;
	}

}

class AppointmentFactoryException extends CException
{

}
