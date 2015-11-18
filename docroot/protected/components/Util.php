<?php

class Util
{
	static public function getPropertyPrices($for = "minimum")
	{

		if ($for == 'maximum') {
			$prices = array_merge([125000, 150000], range(200000, 500000, 50000), range(600000, 1000000, 100 * 1000), range(2000000, 6000000, 1000000));
			$prices = array_combine($prices, Locale::formatMoneyArray($prices));
		} else {
			$prices = array_merge(range(75000, 150000, 25000), range(200000, 500000, 50000), range(600000, 1000000, 100 * 1000));
			$prices = array_combine($prices, Locale::formatMoneyArray($prices));
		}

		return $prices;
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

	static public function enumItem($model, $attribute)
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

	static public function getNearestArrayKey($array, $value)
	{

		foreach ($array as $i) {
			$nearest[$i] = abs($i - $value);
		}
		asort($nearest);

		return key($nearest);
	}

	/**
	 * @param $source
	 * @param $destination
	 * @deprecated since r1234 moved to FileSystem
	 * @see        FileSystem
	 */
	static public function copyDirectory($source, $destination)
	{

		FileSystem::copyDirectory($source, $destination);
	}

	static public function getRandomString($length)
	{

		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string     = '';

		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters) - 1)];
		}

		return $string;
	}

	/**
	 * @param $path
	 * @return bool
	 * @deprecated since r1234 moved to FileSystem
	 * @see        FileSystem
	 */
	public static function createDirectory($path)
	{

		return FileSystem::createDirectory($path);
	}

	/**
	 * Copied from http://php.net/manual/en/function.rmdir.php comment
	 * @param $folderPath
	 * @return bool
	 * @deprecated   since r1234 moved to FileSystem
	 * @see          FileSystem
	 */
	public static function removeDirectory($folderPath)
	{

		return FileSystem::removeDirectory($folderPath);
	}
}
