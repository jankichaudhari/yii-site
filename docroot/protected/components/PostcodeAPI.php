<?php


class PostcodeAPI
{

	private $accCode;
	private $licenseCode;
	private $url;

	private $query;

	private $errors;

	private $result;

	const LOOKUP_FREETEXT    = 'by_freetext';
	const LOOKUP_POSTCODE    = 'by_postcode';
	const LOOKUP_AREA        = 'by_area';
	const LOOKUP_BROWSE      = 'browse';
	const LOOKUP_LOCALITYKEY = 'by_localitykey';
	const LOOKUP_STREETKEY   = 'by_streetkey';

	public function __construct($accCode = null, $licenseCode = null)
	{
		if (isset($accCode, $licenseCode)) {
			$this->accCode     = $accCode;
			$this->licenseCode = $licenseCode;
		} else {
			if (!isset(Yii::app()->params['postcodeAnywhere']['accCode'], Yii::app()->params['postcodeAnywhere']['license'])) {
				throw new CException("postcodeAnywhere parameters are not set up in configuration");
			}

			$this->accCode     = Yii::app()->params['postcodeAnywhere']['accCode'];
			$this->licenseCode = Yii::app()->params['postcodeAnywhere']['license'];
		}

		$this->url = 'http://services.postcodeanywhere.co.uk/xml.aspx';

	}

	/**
	 *
	 * <?xml version="1.0" encoding="UTF-8"?>
	 *     <PostcodeAnywhere Server="WEB-2-2" Version="3.0" Date="22/05/2012 14:11:37" Duration="0.016s">
	 *     <Schema Items="3">
	 *     <Field Name="id" />
	 *     <Field Name="seq" />
	 *     <Field Name="description" />
	 *     </Schema>
	 *       <Data Items="20">
	 *     <Item id="51660633.00" seq="0" description="1 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660634.00" seq="1" description="2 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660635.00" seq="2" description="3 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660636.00" seq="3" description="4 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660637.00" seq="4" description="5 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660638.00" seq="5" description="6 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660639.00" seq="6" description="7 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660640.00" seq="7" description="8 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660641.00" seq="8" description="9 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660642.00" seq="9" description="10 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660643.00" seq="10" description="11 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660644.00" seq="11" description="12 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660645.00" seq="12" description="13 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660646.00" seq="13" description="14 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660647.00" seq="14" description="15 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660648.00" seq="15" description="16 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660649.00" seq="16" description="17 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660650.00" seq="17" description="18 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660651.00" seq="18" description="19 Aulay House 122 Spa Road London SE16" />
	 *     <Item id="51660652.00" seq="19" description="20 Aulay House 122 Spa Road London SE16" />
	 *     </Data>
	 *     </PostcodeAnywhere>
	 * @param        $values
	 * @param string $type
	 * @return PostcodeAPI
	 */
	public function lookup($values, $type = self::LOOKUP_POSTCODE)
	{
		$data        = array('action' => 'lookup',
							 'type'   => $type);
		$data        = array_merge($data, $values);
		$this->query = $this->buildQuery($data);
		return $this;
	}

	/**
	 * @param $id integer needs to be postcodeAnywhereId
	 */
	public function fetchData($id)
	{
		$data = array('action' => 'fetch',
					  'style'  => 'rawgeographic',
					  'id'     => $id);

		$this->query = $this->buildQuery($data);
		return $this;
	}

	public function execute()
	{
		$error = false;

		if (!$this->query) {
			throw new Exception('no query to execute');
		}

		$this->result = $result = file_get_contents($this->query);

		if ($result === false) {
			throw new Exception('cannot execute the query : ' . $this->query . ' server may not respond or query is incorrect');
		}

		$data = simplexml_load_string($result);

		$fields = $data->Schema->Field;
		foreach ($fields as $field) {
			if ($field->attributes()->Name == "error_number") {
				$error = true;
			}
		}
		if ($error) {
			foreach ($data->Data->Item as $item) {
				$this->errors[] = array('code'    => $item->attributes()->error_number,
										'message' => $item->attributes()->message);
			}
		}
		return $this;
	}

	private function buildQuery($data)
	{
		$data['account_code'] = $this->accCode;
		$data['license_code'] = $this->licenseCode;
		return $this->url . "?" . http_build_query($data);
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getPlainResult()
	{
		return $this->result;
	}

	public function getAsArray()
	{
		if ($this->errors) return false;
		$data   = simplexml_load_string($this->result);
		$fields = array();
		foreach ($data->Schema->Field as $key => $field) {
			$fields[] = (string)$field->attributes()->Name;
		}

		$result = array();
		foreach ($data->Data->Item as $key => $item) {
			$t = &$result[];
			foreach ($fields as $field) {
				$t[$field] = (string)$item->attributes()->$field;
			}
		}
		return $result;
	}

	public function getAsXMLObject()
	{
		if ($this->errors) return false;
		return simplexml_load_string($this->result);
	}
}
