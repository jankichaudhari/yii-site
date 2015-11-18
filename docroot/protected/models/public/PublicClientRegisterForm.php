<?php
class PublicClientRegisterForm extends CFormModel
{
	public $id = '';
	public $email = '';
	public $name = '';
	public $surname = '';
	public $telephone = '';
	public $address = '';
	public $minPrice = '';
	public $maxPrice = '';
	public $minBedroom = '';
	public $maxBedroom = '';
	public $bedrooms = '';
	public $currentPosition = '';
	public $type = 'sales';
	public $cv = '';
	public $branch = '';

	public function rules()
	{

		return array(
			array('email, name, telephone', 'required'),
			array('surname, name, address', 'length', 'max' => 100),
			array('minPrice, maxPrice, bedrooms', 'numerical'),
			array('telephone', 'length', 'max' => 40),
			array('type', 'validateType'),
			array('currentPosition', 'safe'),
			array('address', 'type', 'type' => 'string'),
			array('email', 'email'),
			array('email', 'validateClientEmail'),
			array('branch', 'in', 'range' => CHtml::listData(Branch::model()->registerClients()->findAll(), 'bra_id', 'bra_id')),
			array('id', 'safe')
		);
	}

	protected function beforeValidate()
	{

		$this->name      = strip_tags($this->name);
		$this->surname   = strip_tags($this->surname);
		$this->telephone = strip_tags($this->telephone);
		$this->address   = strip_tags($this->address);
		return parent::beforeValidate();
	}

	public function validateType($type, $params)
	{

		if (!in_array($this->$type, ['sales', 'lettings'])) {
			$this->addError('type', 'Type must be selected(Sales or Lettings)');
		}
		return;
	}

	public function getTypes()
	{

		return ['sales' => 'sales', 'lettings' => 'lettings'];
	}

	public function validateClientEmail($email, $params)
	{

		if ($this->$email) {
			/** @var $client Client */
			$criteria    = new CDbCriteria();
			$criteria->compare('cli_email', $this->$email);
			$criteria->with = ['telephones'];
			/** @var $client Client */
			$clients = Client::model()->findAll($criteria);
			if ($client = $clients[0]) {
				$this->name      = $client->cli_fname;
				$this->surname   = $client->cli_sname;
				$this->telephone = $client->telephones[0]->tel_number;
				$this->addError('registeredInfo', 'You are already registered.');
				return false;
			}

		}
	}

	public function register()
	{

		if ($this->validate()) {

			$address = new Address();

			$lines    = explode("\n", $this->address);
			$postcode = '';
			if (count($lines) > 1) {
				$postcode = array_pop($lines);
			}

			for ($i = 0; $i < count($lines) && $i < 5; $i++) {
				$t           = 'line' . ($i + 1);
				$address->$t = $lines[$i];
			}
			$address->postcode = strtoupper($postcode);
			$address->save();

			$client                 = new Client();
			$client->cli_fname      = $this->name;
			$client->cli_sname      = $this->surname;
			$client->cli_email      = $this->email;
			$client->cli_branch     = $this->branch;

			$client->cli_neg        = 0;
			$client->cli_status     = 'Active';
			$client->cli_salemin    = $this->minPrice;
			$client->cli_salemax    = $this->maxPrice;
			$client->cli_salebed    = $this->bedrooms;
			$client->cli_sales      = 'Yes';
			$client->cli_saleemail  = 'Yes';
			$client->cli_salestatus = $this->currentPosition;
			$client->addressID      = $address->id;

			if ($client->save(false)) {
				$telephone             = new Telephone();
				$telephone->tel_type   = 'Other';
				$telephone->tel_number = $this->telephone;
				$telephone->tel_cli    = $client->cli_id;
				$telephone->tel_ord    = 1;

				$telephone->save();
			}
			return true;
		}
	}
}