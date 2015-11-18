<?php
/**
 * Created by JetBrains PhpStorm.
 * User: janki.chaudhari
 * Date: 24/07/12
 * Time: 18:02
 * To change this template use File | Settings | File Templates.
 */
// ================================================================== RefEll

class RefEll {

	var $maj;
	var $min;
	var $ecc;


	/**
	 * Create a new RefEll object to represent a reference ellipsoid
	 *
	 * @param maj the major axis
	 * @param min the minor axis
	 */
	function RefEll($maj, $min) {
		$this->maj = $maj;
		$this->min = $min;
		$this->ecc = (($maj * $maj) - ($min * $min)) / ($maj * $maj);
	}
}