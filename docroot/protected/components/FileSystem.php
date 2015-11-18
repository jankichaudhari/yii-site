<?php

class FileSystem
{
	public static function createDirectory($path)
	{

		$path = rtrim($path, " /") . "/";
		if (!file_exists($path) || !file_exists(dirname($path))) {
			self::createDirectory(dirname($path));
			return mkdir($path, 0777);
		}
	}

	/**
	 * Copied from http://php.net/manual/en/function.rmdir.php comment
	 * @param $folderPath
	 * @return bool
	 */
	public static function removeDirectory($folderPath)
	{

		if (!file_exists($folderPath)) {
			return true;
		}
		if (!is_dir($folderPath) || is_link($folderPath)) {
			return unlink($folderPath);
		}

		foreach (scandir($folderPath) as $item) {
			if ($item == '.' || $item == '..') continue;
			if (!self::removeDirectory($folderPath . "/" . $item)) {
				chmod($folderPath . "/" . $item, 0777);
				if (!self::removeDirectory($folderPath . "/" . $item)) return false;
			}
			;
		}
		return rmdir($folderPath);
	}

	static public function copyDirectory($source, $destination)
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
					copy_directory($PathDir, $destination . '/' . $readDirectory);
					continue;
				}
				copy($PathDir, $destination . '/' . $readDirectory);
			}
			$directory->close();
		} else {
			copy($source, $destination);
		}
	}
}
