<?php

class Date
{
	public static function formatDate($format, $date)
	{

		if (strpos($date, " ") !== false) {
			list($date, $time) = explode(" ", $date);
		} else {
			$time = '';
		}
		if (count(explode("/", $date)) === 2) {
			$date .= "/" . date("Y");
		}

		$date = trim($date . " " . $time);
		$date = str_replace("/", ".", $date);
		return date($format, strtotime($date));
	}

	public static function parseDate($date)
	{
		if (($t = CDateTimeParser::parse($date, 'dd/MM/yyyy')) || ($t = CDateTimeParser::parse($date, 'yyyy-MM-dd'))) {
			return date("Y-m-d", $t);
		}
		return null;
	}
}
