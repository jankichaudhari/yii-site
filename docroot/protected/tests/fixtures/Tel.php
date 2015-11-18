<?php
return array(
	'ExistingClientsPhone1' => array(
		'tel_type'   => Telephone::TYPE_MOBILE,
		'tel_number' => '123 123 123',
		'tel_ord'    => 0,
		'tel_cli'     => $this->getRecord('clients', 'existingClient')->cli_id
	),

	'ExistingClientsPhone2' => array(
		'tel_type'   => Telephone::TYPE_MOBILE,
		'tel_number' => '123 123 123',
		'tel_ord'    => 1,
		'tel_cli'     => $this->getRecord('clients', 'existingClient')->cli_id
	),
);