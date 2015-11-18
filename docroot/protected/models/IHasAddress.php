<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vitaly.suhanov
 * Date: 09/11/12
 * Time: 16:17
 */

interface IHasAddress
{
	/**
	 * returns a reference to the actual IAdress Object
	 *
	 * helper method that in most cases will return $this. in case of property may return related address object in future.
	 *
	 * @return IAddress returns a reference to the actual IAdress Object
	 */
	public function getAddressObject();
}
