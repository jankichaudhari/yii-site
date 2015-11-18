<?php
/**
 * @var                    $this             AppointmentBuilderController
 * @var                    $title            String
 * @var                    $model            Client
 * @var                    $createButtonText String
 */
?>
<?php
$this->widget('AdminGridView', array(
									'id'           => 'client-list',
									'newLayout'    => true,
									'actions'      => array(
										'add' => array(
											$this->createUrl("Client/Create", ['useClient' => true]), $createButtonText,
											['rel' => 'client-list-add-button', 'id' => 'client-add-button']
										),
									),
									'title'        => $title,
									'dataProvider' => $model->search(),
									'columns'      => array(
										array(
											'class'    => 'CButtonColumn',
											'header'   => 'Actions',
											'template' => '{use}{edit}',
											'buttons'  => array(
												'use'  => array(
													'label'       => 'Use',
													'url'         => function (Client $data) {

																return $this->createUrl('clientSelected', ['clientId' => $data->cli_id]);
																return $data->cli_id;
															},
													'imageUrl'    => Icon::USE_ICON,

													'click'       => "selectClient",
													'htmlOptions' => ['class' => 'use']
												),
												'edit' => array(
													'label'    => 'Edit',
													'url'      => function (Client $data) {
																return $this->createUrl('client/update', ['id' => $data->cli_id, 'useClient' => true]);
															},
													'imageUrl' => Icon::EDIT_ICON,
												)
											)
										),
										['name' => 'fullName', 'header' => 'Client\'s name'],
										['name' => 'cli_email', 'header' => 'Client\'s email'],
										array(
											'header' => 'Client\'s phone number', 'value' => function (Client $data) {
													$phones = [];
													foreach ($data->telephones as $phone) {
														$phones[] = $phone->tel_number;
													}
													return implode(", ", $phones);
												}
										),
										array(
											'name'   => 'cli_created',
											'header' => 'Entered on',
											'value'  => function (Client $data) {
														return Date::formatDate("d/m/Y", $data->cli_created);
													}
										),
										array(
											'name'   => '',
											'header' => 'Client\'s address',
											'value'  => function (Client $data) {
														if ($data->address) {
															return $data->address->getFullAddressString(', ');
														}
													}
										),
										array(
											'name'   => '',
											'header' => 'Registered by',
											'value'  => function (Client $data) {

														if ($data->registrator) {
															return $data->registrator->getFullName();
														}
													}
										),

										array(
											'type'   => 'raw',
											'header' => 'e',
											'value'  => function (Client $data) {
														return $data->cli_saleemail === Client::EMAIL_SALES_YES ? CHtml::image(Icon::GREEN_TICK_ICON, 'X') : CHtml::image(Icon::GRAY_CROSS_ICON, 'X');
													}
										),
									),
							   ));

?>
