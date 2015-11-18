<?php


/**
 *
 */
class CsvExporter extends CComponent
{
	/**
	 * @var CDataProvider
	 */
	private $dataProvider;
	/**
	 * @var
	 */
	private $headers;

	/**
	 * @var
	 */
	private $fields;

	/**
	 * @var string
	 */
	private $separator = ',';

	/**
	 *
	 */
	function __construct()
	{

	}

	/**
	 * @param CDataProvider $dataProvider
	 */
	public function setDataProvider(CDataProvider $dataProvider)
	{
		$this->dataProvider = $dataProvider;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function getCSV()
	{
		if (!$this->dataProvider) {
			throw new Exception("Can't export to CSV as DataProvider is not specified");
		}
		$rows = $this->dataProvider->data;
		if (!$this->headers) {
			if($this->fields) {
				$this->headers = implode($this->separator, array_intersect(array_keys(reset($rows)), $this->fields));
			} else {
				$this->headers = implode($this->separator, array_keys(reset($rows)));
			}
		} else  {
			$this->headers = implode($this->separator, (array) $this->headers);
		}
		$data = array();
		foreach ($rows as $value) {
			if($this->fields) {
				$value = array_intersect_key($value, $this->fields);
				foreach ($this->fields as $f) {
					$t[$f] = $value[$f];
				}
				$value = $t;

			}
			$value = array_map(function($str) {
				return '"' . str_replace('"', '""', $str) . '"';
			}, $value);
			$data[] = implode($this->separator, $value);
		}
		return $this->headers . "\n" . implode("\n", $data);
	}

	/**
	 * @param $fields
	 * @return CsvExporter
	 */
	public function setFields($fields)
	{
		$this->fields = array_combine($fields, $fields);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @return mixed
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * @param $headers
	 * @return CsvExporter
	 */
	public function setHeaders($headers)
	{
		$this->headers = $headers;
		return $this;
	}

}