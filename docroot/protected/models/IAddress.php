<?php
/**
 * @author Vitaly
 */
interface IAddress extends IHasAddress
{
	/**
	 * Constant to acquire postcodes first part which comes before space.
	 */
	const POSTCODE_PART_ONE = 0;
	/**
	 * Constant to acquire postcodes second part which comes after space.
	 */
	const POSTCODE_PART_TWO = 1;

	/**
	 *    returns an array of all lines in the address.
	 *
	 * key should represent line's number.
	 * If address line is not specified should return empty value for corresponding key
	 * i.e.
	 *
	 *  must follow the contract <code>count(IAddress::getAllLines()) == IAddress::getLinesCount()</code>
	 * <code>
	 * $array = Array(
	 *	'1' => 'Line 1',
	 *	'2' => 'Line 2',
	 *	'3' => '',
	 *	'4' => 'Line 4',
	 *	'5' => ''
	 * )
	 * </code>
	 *
	 * @return Array of all lines in the address.
	 */
	public function getAllLines();

	/**
	 * returns single line of address which number is $line
	 *
	 * @param $line int number of line  to be returned
	 * @return String returns single line which number is $line
	 *
	 * @throws OutOfBoundsException if $line is less than first line or greater than IAddress::getLinesCount()
	 */
	public function getLine($line);

	/**
	 * returns total number of lines that this IAddress may represent.
	 *
	 * @return int
	 */
	public function getLinesCount();

	/**
	 * returns postcode of the address.
	 *
	 * @return String postcode of the address.
	 */
	public function getPostcode();

	/**
	 * returns one part of the postcode.
	 *
	 * Most commonly first part of postcode may be used independently.
	 * Also may be used to retrieve a second part.
	 *
	 * @param int $part which part of the postcode to return. accepts two values <code>IAddress::POSTCODE_POART_ONE</code> or <code>IAddress::POSTCODE_POART_TWO</code>
	 * @return String requested part of the postcode
	 */
	public function getPostcodePart($part = IAddress::POSTCODE_PART_ONE);

	/**
	 * returns Latitude of the address if it has one; null otherwise.
	 *
	 * @return Float|null returns Latitude of the address if it has one; null otherwise.
	 */
	public function getLat();

	/**
	 * returns longitude of the address if it has one; null otherwise.
	 *
	 * @return Float|null returns longitude of the address if it has one; null otherwise.
	 */
	public function getLng();

	/**
	 * returns ID of the address in the postcodeAnywhere database if it has one; null otherwise.
	 *
	 * @return int|null returns ID of the address in the postcodeAnywhere database if it has one; null otherwise.
	 */
	public function getPostcodeAnywhereId();

	/**
	 *
	 *
	 * @return String
	 */
	public function getCity();

	/**
	 * @param string $separator
	 * @return String
	 */
	public function getFullAddressString($separator = '<br>');
	/**
	 * @return PropertyArea|null returns Area object fi it exists or null otherwise.
	 */
	public function getAreaObject();
}
