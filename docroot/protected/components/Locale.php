<?php

class Locale
{
	static public function metersToFeet($value)
	{

		return round(($value * 1000000) / 92903.04);
	}

	/**
	 * @deprecated
	 *
	 * @param        $price
	 * @param bool   $let
	 * @param string $pm
	 * @return string
	 */
	static public function formatPrice($price, $let = false, $pm = 'p/w')
	{

		$price = (float)preg_replace('/[^0-9.]/', '', $price);

		if ($let) {
			return '&pound;' . number_format($price) . ' ' . ($pm && $pm == 'pcm' ? 'pcm' : 'p/w');
		} else {
			return '&pound;' . number_format($price);
		}
	}

	static public function formatCurrency($number, $withSymbol = true, $htmlEncode = true)
	{
		$number = (float)preg_replace('/[^0-9.]/', '', $number);
		$number = number_format($number);
		if ($withSymbol) {
			$number = '£' . $number;
		}
		return $htmlEncode ? htmlentities($number) : $number;
	}

	static public function formatPhone($phoneNumber)
	{

		return $phoneNumber;
	}

	static public function formatMoneyArray($amountArray)
	{

		$resultArray = array();
		foreach ($amountArray as $amount) {
			$resAmt = '£' . number_format($amount);
			array_push($resultArray, $resAmt);
		}
		return $resultArray;
	}

	static public function strapString($string, $minLength, $maxLength)
	{

		$thisStrapline = $string;
		if (strlen($string) > $maxLength) {
			$thisStrapline = substr($string, $minLength, $maxLength);
			$temp          = strrpos($thisStrapline, ' ');
			$thisStrapline = substr($string, $minLength, $temp) . '...';
		}
		return $thisStrapline;
	}

	/**
	 * @param $model
	 * @param $attribute
	 * @return array
	 * @deprecated
	 */
	public static function enumItem($model, $attribute)
	{

		$attr   = $attribute;
		$values = array();
		preg_match('/\((.*)\)/', $model->tableSchema->columns[$attr]->dbType, $matches);
		foreach (explode(',', $matches[1]) as $value) {
			$value          = str_replace("'", null, $value);
			$values[$value] = Yii::t('enumItem', $value);
		}

		return $values;
	}

	public static function copyDirectory($source, $destination)
	{

		if (is_dir($source)) {
			@mkdir($destination);
			$directory = dir($source);
			while (false !== ($readDirectory = $directory->read())) {
				if ($readDirectory == '.' || $readDirectory == '..') {
					continue;
				}
				$PathDir = $source . '/' . $readDirectory;
				if (is_dir($PathDir)) {
					self::copyDirectory($PathDir, $destination . '/' . $readDirectory);
					continue;
				}
				copy($PathDir, $destination . '/' . $readDirectory);
			}
			$directory->close();
		} else {
			copy($source, $destination);
		}
	}

	public static function getRandomString($length)
	{

		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string     = '';

		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters) - 1)];
		}

		return $string;
	}

	public static function isMobile($number)
	{
		$number = preg_replace('/[^0-9]/', '', $number);
		if (preg_match('/^07/', $number)) { // starts with 07
			$number = preg_replace('/^07/', '447', $number);
		}
		if (!preg_match('/^447/', $number) || strlen($number) !== 12) { // doesn't start with 07 nor 447
			return false;
		}
		return true;
	}
}
