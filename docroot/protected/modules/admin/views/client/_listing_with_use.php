<?php
/**
 * @var $title
 * @var $this  ClientController
 * @var $model Client
 */
?>

<?php
$this->widget('AdminGridView', array(
		'id'           => 'client-list',
		'newLayout'    => true,
		'actions'      => array(
				'add' => array(
						$this->createUrl("Create", array('callback' => 'useClient')),
						'Add New Client',
						array('rel' => 'client-list-add-button')
				),
		),

		'title'        => 'Select Client',
		'dataProvider' => $model->search(),
		'columns'      => array(
				array(
						'class'    => 'CButtonColumn',
						'header'   => 'Actions',
						'template' => '{use}',
						'buttons'  => array(
								'use' => array(
										'label'       => 'Use',
										'url'         => function ($data) {

													return $data->cli_id;
												},
										'imageUrl'    => Icon::USE_ICON,
										'click'       => "selectClient",
										'htmlOptions' => ['class' => 'use']
								),
						)
				),
				array(
						'name'        => 'cli_id', 'header' => 'Id',
						'htmlOptions' => array('style' => "width:30px;")
				),
				array('name' => 'fullName', 'header' => 'Name'),
				array('name' => 'cli_email', 'header' => 'Email'),
				array(
						'header' => 'Phone numbers',
						'type'   => 'raw',
						'value'  => function (Client $data) {

									$phones = array();
									foreach ($data->telephones as $phone) {
										$phones[] = CHtml::link($phone->tel_number, "tel://{$phone->tel_number}");
									}
									return implode(", ", $phones);
								}
				),
				array(
						'name'  => 'cli_created', 'header' => 'Entered',
						'value' => function (Client $data) {

									return Date::formatDate("d/m/Y", $data->cli_created);
								}
				),
				array(
						'name'  => '', 'header' => 'Address',
						'value' => function (Client $data) {

									if ($data->address) {
										$line1 = ($data->address->line1) ? $data->address->line1 . ',' : '';
										$line2 = ($data->address->line2) ? $data->address->line2 . ',' : '';
										$line3 = ($data->address->line3) ? $data->address->line3 . ',' : '';
										$line4 = ($data->address->line4) ? $data->address->line4 . ',' : '';
										$line5 = ($data->address->line5) ? $data->address->line5 . ',' : '';

										$fullAddress = $line1 . ' ' . $line2 . ' ' . $line3 . ' ' . $line4 . ' ' . $line5 . ' ' . $data->address->postcode;
										return $fullAddress;
									}
								}
				),
				array(
						'name'   => '',
						'type'   => 'raw',
						'header' => 'Reg\'d',
						'value'  => function (Client $data) {

									if ($data->registrator) {
										return '<span class="negotiator" style="background: #' . $data->registrator->use_colour . '"></span>' . $data->registrator->getInitials();
									}
								}
				),
		),
));
?>