<?php
include_once dirname(__FILE__) . '/../bootstrap.php';
class PlaceImagesTestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("Place's images");
		$suite->addTestSuite('MainGalleryImageTest');
	}
}