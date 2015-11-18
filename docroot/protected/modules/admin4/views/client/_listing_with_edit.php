<?php
/**
 * @var $this            ClientController
 * @var $dataProvider    CDataProvider
 * @var $title           String
 * @var $addButton       bool
 */
$addBtn = $addButton ? ['add' => [$this->createUrl("Create", ['showDetailsAfter' => true]), 'Add New Client', ['rel' => 'client-list-add-button']]] : [];
$this->widget('AdminGridView', array(
									'id'           => 'client-list',
									'actions'      => $addBtn,
									'title'        => $title ? $title : 'Client Search',
									'dataProvider' => $dataProvider,
									'columns'      => array(
										array(
											'class'    => 'CButtonColumn',
											'header'   => 'Actions',
											'template' => '{edit}',
											'buttons'  => array(
												'edit' => array(
													'label'    => 'Edit',
													'url'      => function ($data) {
																return $this->createUrl('update', ['id' => $data->cli_id]);
															},
													'imageUrl' => Icon::EDIT_ICON,
												)
											)
										),
										['name' => 'cli_id', 'header' => 'Id', 'htmlOptions' => ['style' => "width:30px;"]],
										['name' => 'fullName', 'header' => 'Name'],
										['name' => 'cli_email', 'header' => 'Email'],
										array(
											'header' => 'Phone number',
											'type' => 'raw',
											'value' => function (Client $data) {

													$phones = array();
													foreach ($data->telephones as $phone) {
														$phones[] = CHtml::link($phone->tel_number, "tel://{$phone->tel_number}");
													}
													return implode(", ", $phones);
												}
										),
										array(
											'name'  => 'cli_created', 'header' => 'Entered on',
											'value' => function (Client $data) {
														return Date::formatDate("d/m/Y", $data->cli_created);
													}
										),
										array(
											'name'  => '', 'header' => 'Address',
											'value' => function (Client $data) {
														if ($data->address) {
															return $data->address->getFullAddressString(', ');
														}
													}
										),
										array(
											'name'   => 'registrator',
											'header' => 'Registered by',
											'value'  => function (Client $data) {
														if ($data->registrator) {
															return $data->registrator->getFullName();
														}
													},
										),
										array(
											'type'              => 'raw',
											'header'            => '@',
											'value'             => function (Client $data) {
														return $this->getStatusIcon($data->cli_saleemail === Client::EMAIL_SALES_YES);
													},
											'headerHtmlOptions' => ['title' => 'Email offers']
										),
									),
							   ));
